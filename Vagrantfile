# -*- mode: ruby -*-
# # vi: set ft=ruby :

# How many machines do we need to provision
cluster_size = 4

# VM parameters
vm_gui = false
vm_cpus = 1
vm_memory = 1024
vb_cpuexecutioncap = 100

# This function will be used to customize colud-config yaml
def customize_cloud_config(cloud_init_yaml, vm_i)
    case vm_i
        when 1 then cloud_init_yaml['coreos']['fleet']['metadata'] = 'role=head'
        when 2 then cloud_init_yaml['coreos']['fleet']['metadata'] = 'role=proxy'
        when 3 then cloud_init_yaml['coreos']['fleet']['metadata'] = 'role=web'
        when 4 then cloud_init_yaml['coreos']['fleet']['metadata'] = 'role=web'
    end
end

# If this is a run of 'vagrant up', then generate the cloud-init files for each of the VMs
if ARGV[0].eql?('up')
    require 'open-uri'
    require 'yaml'

    etcd_token_gen_url = "https://discovery.etcd.io/new?size=%d" % [cluster_size]
    etcd_service_token = open(etcd_token_gen_url).read

    cloud_init_yaml = YAML.load(IO.readlines('cloud-init-template.yaml')[1..-1].join)

    (1..cluster_size).each do |i|
      cloud_init_yaml['coreos']['etcd2']['discovery'] = etcd_service_token
      customize_cloud_config(cloud_init_yaml, i)

      File.open('generated-cloud-init-files/cloud-init-%02d' % [i], 'w') do |file|
        file.write("#cloud-config\n\n%s" % [YAML.dump(cloud_init_yaml)])
      end
    end
end

Vagrant.configure("2") do |config|
  ## use Vagrant's insecure key and forward ssh agent
  config.ssh.insert_key = false
  config.ssh.forward_agent = true

  # We use the current stable version of CoreOS
  # We assume that VM is provisioned using VirtualBox
  config.vm.box = "coreos-stable"
  #config.vm.box_version = "current"
  config.vm.box_url = "https://storage.googleapis.com/stable.release.core-os.net/amd64-usr/current/coreos_production_vagrant.json"

  # CoreOS does not support VirtualBox guest additions 
  config.vm.provider :virtualbox do |v|
    v.check_guest_additions = false
    v.functional_vboxsf     = false
  end

  # prevent the vagrant-vbguest plugin conflict
  if Vagrant.has_plugin?("vagrant-vbguest") then
    config.vbguest.auto_update = false
  end

  # Provision the machines
  (1..cluster_size).each do |i|
    config.vm.define vm_name = "core-%02d" % [i] do |config|
      config.vm.hostname = vm_name

      config.vm.provider :virtualbox do |vb|
        vb.gui = vm_gui
        vb.cpus = vm_cpus
        vb.memory = vm_memory
        vb.customize ["modifyvm", :id, "--cpuexecutioncap", "#{vb_cpuexecutioncap}"]
      end

      # Assign the ip address
      config.vm.network :private_network, ip: "172.17.8.%d" % [i+100]

      # Map the current direcory to /vagrant inside the VM
      config.vm.synced_folder ".", "/home/core/share" #, nfs: true, id: "core", mount_options: ['nolock,vers=3,udp']

      config.vm.provision "shell", inline: "echo '172.17.8.90 hub-proxy' >> /etc/hosts"

      # Copy the VM-specific cloud-config file to the new VM
	    cloud_config_path = 'generated-cloud-init-files/cloud-init-%02d' % [i]
      config.vm.provision :file, :source => "#{cloud_config_path}", :destination => "/tmp/vagrantfile-user-data"
      config.vm.provision :shell, :inline => "mv /tmp/vagrantfile-user-data /var/lib/coreos-vagrant/", :privileged => true

      # Copy the bash history file
      config.vm.provision :file, :source => "bash-hist.txt", :destination => "/tmp/bash-hist.txt"
      config.vm.provision :shell, :inline => "cat /tmp/bash-hist.txt > /home/core/.bash_history; chown core:core /home/core/.bash_history", :privileged => true
    end
  end


  # Provision a machine that will run a Docker Hub proxy; it does not need to be a part of the cluster 
  config.vm.define vm_name = "core-hub-cache" do |config|
    config.vm.hostname = vm_name

    config.vm.provider :virtualbox do |vb|
      vb.gui = false
      vb.cpus = 1
      vb.memory = 1024
      vb.customize ["modifyvm", :id, "--cpuexecutioncap", "#{vb_cpuexecutioncap}"]
    end

    # Assign the ip address
    config.vm.network :private_network, ip: "172.17.8.90"

    # Map the current direcory to /vagrant inside the VM
    config.vm.synced_folder ".", "/home/core/share", nfs: true, id: "core", mount_options: ['nolock,vers=3,udp']
    config.vm.synced_folder "./registry-cache", "/home/core/registry", nfs: true, id: "core", mount_options: ['nolock,vers=3,udp']

    config.vm.provision "shell", inline: "echo '172.17.8.90 hub-proxy' >> /etc/hosts"

    config.vm.provision :file, :source => "docker-hub-proxy.service", :destination => "/tmp/docker-hub-proxy.service"
    config.vm.provision "shell", inline: "cat /tmp/docker-hub-proxy.service > /etc/systemd/system/docker-hub-proxy.service"
    
    config.vm.provision "shell", inline: "systemctl enable docker-hub-proxy.service"
    config.vm.provision "shell", inline: "systemctl start docker-hub-proxy.service"
  end

end

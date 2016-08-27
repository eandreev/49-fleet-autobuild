vagrant destroy -f
vagrant up
sleep 30
vagrant ssh core-01 -- -A share/fleetctl-init.sh
vagrant ssh core-04 -- -A


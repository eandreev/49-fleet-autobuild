vagrant destroy -f core-0{1..4}
vagrant up core-0{1..4}
sleep 30
vagrant ssh core-01 -- -A share/fleetctl-init.sh
vagrant ssh core-04 -- -A


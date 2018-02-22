# -*- mode: ruby -*-
# vi: set ft=ruby :
Vagrant.configure(2) do |config|
  config.vm.provider "virtualbox" do |v|
    v.memory = 1000 
    v.cpus = 1 
  end
  config.vm.define  "php7" do |host|
    host.vm.box = "centos/7"
    host.vm.hostname = "php7.example"
    host.vm.network "private_network", ip: "192.168.50.51"
    host.vm.provision "shell", path: "strap_php"
    host.vm.network "forwarded_port", guest: 80, host: 5555 
  end
end



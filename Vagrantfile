# vagrant init ubuntu/xenial64

Vagrant.configure("2") do |config|
    config.vm.box = "ubuntu/xenial64"

    config.vm.network "forwarded_port", guest: 3306, host: 3307 

    config.vm.provider :virtualbox do |v|
        v.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
        v.customize ["modifyvm", :id, "--memory", 1024]
        v.customize ["modifyvm", :id, "--name", "Monolith Twig HTML Templating Component"]
    end

    config.vm.provision "shell" do |s|
        s.inline = "sudo apt-get update && sudo apt-get install -y python"
    end

    config.vm.provision "ansible" do |ansible|
        ansible.playbook = "virtual-machine/provision.yml"
        ansible.extra_vars = {
            hostname: "twig-templating",
            install_db: "no",
            install_ohmyzsh: "yes",
            install_web: "yes",
            install_redis: "no",
            enable_swap: "yes",
            swap_size_in_mb: "1024"
        }
    end
end

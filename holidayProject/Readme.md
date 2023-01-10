> # **You are required to perform the following tasks**
>
> - Set up 2 EC2 instances on AWS(use the free tier instances).
>
> - Deploy an Nginx web server on these instances(you are free to use Ansible)
> - Set up an ALB(Application Load balancer) to route requests to your EC2 instances
> - Make sure that each server displays its own Hostname or IP address. You can use any programming language of your choice to display this.
> - Work on building a personal portfolio and CV (Check out resumeworded.com).
>
>> Important points to note:
>>
>> -  I should not be able to access your web servers through their respective IP addresses. Access must be only via the load balancer
>> - You should define a logical network on the cloud for your servers.
>> - Your EC2 instances must be launched in a private network.
>> - Your Instances should not be assigned public IP addresses.
>> - You may or may not set up auto scaling(I advice you do for knowledge sake)
>> - You must submit a custom domain name(from a domain provider e.g. Route53) or the ALB’s domain name.


# **Method 1 - Without creating a logical Network**

# Step 1 - Setting Up EC2 Instances

- Create two Ubuntu EC2 instance
- Add the following script to the userdata. Deploying Nginx web server on EC2 instances
```
#!/bin/bash
sudo apt-get update -y
sudo apt-get upgrade -y
sudo apt-get install nginx -y
sudo apt-get install php8.1-fpm -y
```

- Connect to the Ec2 instance
- Create a load Balancer and target group targeting the two instances created at the top

# Step 2 - Setting Up the Nginx Web Server and config

- SSH into the EC2 instance and run the following commands

```
sudo nano /etc/nginx/sites-available/default
```

- Edit the default file configuration to the following

```
server {
    listen 80 default_server;
    listen [::]:80 default_server;

    root /var/www/html;

    index index.php index.html index.htm index.nginx-debian.html;

    server_name _;

    location / {
        try_files $uri $uri/ =404;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.1-fpm.sock;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

- After editing the default file. Navigate to the html folder

```
cd /var/www/html
```

- Create a new file with the name index.php

```
sudo nano index.php
```

***N.B: See the index.php file in this repo***

# Step 3 - Configure Route53


**Method 2 - With a Logical network**
# Step 1 - Create vpc, subnets, route tables, internet gateway, nat gateway, security groups, bastion host, private instances, and configure the route tables.
- Set up 2 private EC2 instances on AWS(use the free tier instances).
- Created demovpc with the cidr range of 198.162.0.0/16
- Created private subnet with cidr range of 192.168.3.0/24
- created public subnet with cidr range of 192.168.2.0
- Created private route table and associated it with the private subnet.
- Created internet gateway and attached it to the demovpc.
- Created public route table and associated it with the public subnet.
- Created a route in the public route table with the destination of 0.0.0.0/0 and a target of the internet gateway.
- Created a route in the private route table with the destination of 0.0.0.0/0 and a target of the nat gateway.
- Created a security group called demoSGnew and added the following rules: inbound (tcp, 22, 0.0.0.0/0 : icmp, all, 0.0.0.0/0) Outbound: all traffic

# Step 2 - Set up Bastion host
- Create an EC2 instance, assigned to the public subnet, with the security group demoSGnew. Also enable auto assign public Ip

# Step 3 - Set up Private instance
- Create an EC2 instance, assigned to the private subnet, with the security group demoSGnew. Disable auto assign public Ip.

# Step 4 - Set up Nginx web server on the private instance
- SSH into the bastion host
- Created a new file. Copy the .pem key used to connect to the bastion into the new file
```
chmod 400 newfile.pem
```
```
ssh -i newfile.pem ubuntu@ipaddress
```
```
sudo apt-get update -y
sudo apt-get upgrade -y
sudo apt-get install nginx -y
sudo apt-get install php8.1-fpm -y
sudo nano /etc/nginx/sites-available/default
```
- Edit the config file (See details above)
- After editing the file. Cd into the html file
```
cd /var/www/html
sudo nano index.php
```

Do the samee for the second private instance.

# step 5 - Set up Load Balancer
- Create a load balancer and target group targeting the two private instances created at the top
- Create a listener on port 80 and forward to the target group
- Create a listener on port 443 and forward to the target group

# Step 6 - Set up Route53
- Create a hosted zone
- Create a record set with the name of the load balancer and the type of A - IPv4 address


N.B: EC2 security group should allow http only access to the load balancer
load bancer security group should allow http access to everyone


# **RESOURCES**
- https://docs.aws.amazon.com/Route53/latest/DeveloperGuide/routing-to-elb-load-balancer.html

- A simple guide to install and Configure Nginx to serve PHP files. - https://www.youtube.com/watch?v=44G8sTXPDhk
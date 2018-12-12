
<pre>
 Some notes on how I set this up on digitalocean.

 After creating an account, did Create Droplet.
 Choices:
  Choose an image - Debian 9.1x64 (the most recent version).

  Choose a size- went for the $40 / month one with 2 CPUs, 4G memory, a 60G root volume and 4TB free transfer.
  Add block storage- choose the 1000G one (we are currently using less than 400G) which is $100/mo.
  Choose a datacenter region- Frankfurt, which was the default.  It's pretty central I guess.
  Select additional options- chose backups (which is an extra 20% of the cost), and monitoring.
  Add your ssh keys- I cat'ed my ~/.ssh/id_rsa.pub and added it there, with the name dans_ssh
  Choose a hostname- chose openslr-server  [note: it also hosts several other sites, but this
    is responsible for most of the traffic.]

  Clicked 'Create'.  It gave me IP address 46.101.158.64.

  Added to my ~/.ssh/config the following:

Host openslr
     Hostname 46.101.158.64
     User root
     IdentityFile ~/.ssh/id_rsa

 I was able to ssh there.


 Install some important packages:
   apt-get install emacs apache2 libapache2-mod-php git vim awstats


cd /var/www
git clone https://github.com/danpovey/kaldi-asr.git  kaldi-asr
git clone https://github.com/danpovey/openslr.git  openslr
git clone https://github.com/danpovey/danielpovey.git  danielpovey

I did a bunch of stuff in the /etc/apache2 directory, copying stuff from the old
setup and mixing with the default contents.

I copied the entire directory /etc/letsencrypt.  It seems it was created by
the letsencrypt package, now renamed to certbot, but that was not installed
on the previous image; maybe it was running on a previous image.


on digitalocean.com I went to the 'volumes' tab of the node, to set up the volume.
It told me how to configure it as follows: (via more -> config instructions, although
I changed the name a bit):


 mkfs.ext4 -F /dev/disk/by-id/scsi-0DO_Volume_volume-fra1-02
 mkdir -p /mnt/web-data

 mount -o discard,defaults /dev/disk/by-id/scsi-0DO_Volume_volume-fra1-02 /mnt/web-data

  echo '/dev/disk/by-id/scsi-0DO_Volume_volume-fra1-02 /mnt/web-data ext4 defaults,nofail,discard 0 0' | tee -a /etc/fstab

==

 I rsync'd /mnt/web-data from the old server.

 chown -R www-data:www-data /mnt/web-data/kaldi-asr-data/tmp
==
in /var/www/kaldi-asr, I had to run
 scripts/setup_temp_space.sh
actually it worked if I ran the commands one by one, but not just by itself.
or maybe I did it in the wrong order with other stuff; not sure.
that stuff is only needed for the now-outdated downloads setup.

==
#set up some links in /mnt, not sure if all of these are still needed but what the hell:

 cd /mnt
 ln -s web-data/data   web-data/kaldi-asr-data web-data/resources1 web-data/kaldi-repos .

#set up the loopback device for kaldi-asr:
 cd /var/www/kaldi-asr; scripts/setup_temp_space.sh







</pre>


## Let's Encrypt certificates installation

DISCLAIMER: I(vpanayotov) don't really know anything about Let's Encrypt beyond
skimming through a few web pages. On the other hand one of the goals of that project
is to make SSL encrypton accessible to the clueless masses, which makes a model
user of theirs, I guess. The point is: take everything that follows w/ a grain of salt.


### Helpful guides

The installation instructions on Let's Encrypt's official site seem are fairly
straightforward, but when installing it for the first time I've found this tutorial
to be helpful: https://hblok.net/blog/posts/2016/02/24/lets-encrypt-tls-certificate-setup-for-apache-on-debian-7/
It also discusses how to test your config for vulnurable ciphers and stuff, but
that sort of attention to detail is not really necessary in our case.

Mostly you copy your :80 vhost config, and add a few lines such as:

```
ServerName danielpovey.com
SSLEngine On
SSLCertificateFile /etc/letsencrypt/live/danielpovey.com/cert.pem
SSLCertificateKeyFile /etc/letsencrypt/live/danielpovey.com/privkey.pem
SSLCertificateChainFile /etc/letsencrypt/live/danielpovey.com/chain.pem
```

I think Let's Encrypt's scripts might actually be able to do this sort of stuff
automatically.


### Copy existing certificates to a new server

If you are migrating your installation to a new server you can just use the same
/etc/letsencrypt directory from the old server.

```
cd /etc
tar -czf letsencrypt.tar.gz letsencrypt
scp letsencrypt.tar.gz <user>@<new-server>:/etc/

[logon to the new server]
cd /etc
tar xf letsencrypt.tar.gz
```

It's preferable to transfer the contents in an archive in order to preserve the
symlinks- otherwise the certbot script will complain. Assuming you've also preserved
your Apache configs, HTTPS access should just work.


### Installation of certificate renewal scripts

The official docs recommend using the python-certbot-apache package on Debian 9(our
current "new" server). Our existing configs(see previous section) were created using
v0.17 from GitHub, and Debian 9 ships w/ v0.10 and "certbot" complains about that.
So I've decided to download the latest and greatest "stable"(?) release at the moment
which is [v0.19.0](https://github.com/certbot/certbot/archive/v0.19.0.tar.gz).

```
cd /root
wget https://github.com/certbot/certbot/archive/v0.19.0.tar.gz
tar xf v0.19.0.tar.gz
./certbot-0.19.0/letsencrypt-auto --help
```

The last command actually installed a bunch of Debian packages the first time I ran
it. We can then try and see if it works:

```
./certbot-0.19.0/certbot-auto renew --dry-run
Saving debug log to /var/log/letsencrypt/letsencrypt.log

-------------------------------------------------------------------------------
Processing /etc/letsencrypt/renewal/danielpovey.com.conf
-------------------------------------------------------------------------------
Cert not due for renewal, but simulating renewal for dry run
Plugins selected: Authenticator apache, Installer apache
Renewing an existing certificate
Performing the following challenges:
tls-sni-01 challenge for danielpovey.com
tls-sni-01 challenge for www.danielpovey.com
Encountered vhost ambiguity when trying to find a vhost for www.danielpovey.com but was unable to ask for user guidance in non-interactive mode. Certbot may need vhosts to be explicitly labelled with ServerName or ServerAlias directives.
Falling back to default vhost *:443...
Waiting for verification...
Cleaning up challenges

-------------------------------------------------------------------------------
new certificate deployed with reload of apache server; fullchain is
/etc/letsencrypt/live/danielpovey.com/fullchain.pem
-------------------------------------------------------------------------------

-------------------------------------------------------------------------------
** DRY RUN: simulating 'certbot renew' close to cert expiry
**          (The test certificates below have not been saved.)

Congratulations, all renewals succeeded. The following certs have been renewed:
  /etc/letsencrypt/live/danielpovey.com/fullchain.pem (success)
  ** DRY RUN: simulating 'certbot renew' close to cert expiry
  **          (The test certificates above have not been saved.)
  -------------------------------------------------------------------------------
```

As shown above, the script complains about the :443(i.e. SSL) vhost's configuration
is in the same config file as the regular :80(HTTP) vhost, but according to a forum
post I've found, the message is actually benign.

### Automatic renewal configuration

We modify root's crontab:

```
crontab -e

[paste the following line]
52 0,12 * * *  /root/certbot-0.19.0/certbot-auto renew --apache --quiet
```

This should obtain a new certificate when deemed necessary.
If we execute that command at the cmdline:

```
/root/certbot-0.19.0/certbot-auto renew --apache
Saving debug log to /var/log/letsencrypt/letsencrypt.log

-------------------------------------------------------------------------------
Processing /etc/letsencrypt/renewal/danielpovey.com.conf
-------------------------------------------------------------------------------
Cert not yet due for renewal

-------------------------------------------------------------------------------

The following certs are not due for renewal yet:
  /etc/letsencrypt/live/danielpovey.com/fullchain.pem (skipped)
  No renewals were attempted.
  -------------------------------------------------------------------------------
```

NOTE: It's probably a good idea to disable the renewal command in the old server's
crontab, in order to avoid conflict, even though I'd expect the request from the
old server to fail due to DNS record for danielpovey.com being changed(not familiar
with the ACME protocol though).


### Some useful certificate maintenance commands (certbot v0.20)

* List all certificates installed on a server

    $ ./certbot-auto certificates


* Revoke certificate

    # the certbot will ask you if you want to also detele the certificate
    $ ./certbot-auto revoke --cert-path /etc/letsencrypt/live/openslr.org/fullchain.pem


* Update a certificate(e.g. www.openslr.org), by adding a new domain (e.g. openslr.org)

    $ ./certbot-auto certonly --cert-name www.openslr.org -d www.openslr.org -d openslr.org

### Example of enabling Let's Encrypt for a site

Assuming that we have a site alredy configured to serve non-encrypted trafic on port 80,
the following commands can be used to configure SSL:

* Create new certificate(s)- with Let's Encrypt we need both example.org and www.example.org (no support for wildcard certificates):

  ./letsencrypt-auto certonly --apache -d kaldi-asr.org -d www.kaldi-asr.org

    The "--apache" instructs the bot to use the already running Apache server for its requests. Otherwise, if for example the "--standalone" option is used it complains about not being able to bind to port 80.


* Edit the config file for the site. Just copy and paste(make another copy) the entire "<VirtualHost *:80>" section, change the ":80" part to ":443" and add options to enable SSL:

       SSLEngine On
       SSLCertificateFile /etc/letsencrypt/live/kaldi-asr.org/cert.pem
       SSLCertificateKeyFile /etc/letsencrypt/live/kaldi-asr.org/privkey.pem
       SSLCertificateChainFile /etc/letsencrypt/live/kaldi-asr.org/chain.pem

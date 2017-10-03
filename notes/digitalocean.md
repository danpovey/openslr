
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


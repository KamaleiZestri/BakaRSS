# Baka RSS
Why doesn't [MangaUpdates](https://www.mangaupdates.com/index.html) have an rss feed of JUST the manga you are reading? Well now it can. with the help of [XAMPP](https://www.apachefriends.org/index.html).

## Pre-requisetes
Know how to use RSS and already a feed reader of your liking. The one I use is QuiteRSS, but this works with any locally hosted feed reader.

## Instructions
_1. Setting up MangaUpdates_\
Set up an account on [MangaUpdates](https://www.mangaupdates.com/index.html) and have a Reading list. MAKE SURE YOUR LIST IS PUBLIC and copy down that public url. It is something of the form `https://www.mangaupdates.com/mylist.html?id=xxxxxx&list=read`.![Manga Updates Settings](img\mangaupdates_settings.png)

_2. Download XAMPP/Decide how to run this._\
Technically, you do not need XAMPP for this to work. Any way you get PHP to run via an IP address will work.

Just download XAMPP for simplicities sake. The rest of these instructions assume so.

_3. Using XAMPP_\
Find `Apache Web Server` and start it. That's all. You may also want to lookup how to have XAMPP automatically start with your computer.

_4. Download Baka RSS_\
Click on the `Code` button on the top right of this webpage. Then click on `Download ZIP`. Then extract it's contents to the `htdocs` folder. For Windows, this is most likely at `c:xampp/htdoc`. ![Inside htdocs folder](img\bakarss_folder.png)

_5. Create settings_\
Inside the bakarss folder, create a file called `settings.txt` and open it in your favorite text editor. Rmember that URL for your reading list in step one? Paste it into here like this, save, then close the file. ![](img\bakarss_folder_settings.png)

_6. Add to your reader_\
Just add the URL `http://localhost/bakarss/bakarss.php` to your reader. As long as you have Apache Web Server on XAMPP running, this link will work.
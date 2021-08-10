<?php
function loadSettings()
{
    $xml = simplexml_load_file("settings.xml");
    define("MYLIST",$xml->reading);
}
/*
    Goes the given $url and performs the given $regex.
*/
function getList($url, $reg, $x)
{
   
    $mangaList = array();

    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL,$url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);

    $result= curl_exec($curl);

    preg_match_all($reg, $result, $matches,PREG_SET_ORDER);

    for($i=0; $i<count($matches); $i++)
    {
        $manga = array();
        for($j=1; $j<=$x; $j++)
        {
            array_push($manga,$matches[$i][$j]);
        }

        array_push($mangaList,$manga);
    }

    curl_close($curl);
    return $mangaList;
};
 /*
    Release List
        [0](manga1)
            [0] Link to manga , [1] Title, [2] chapter number, [3] Link to Group, [4] Group
        [1](manga2)
*/
function getLatestReleaseList()
{
    $link = "https://www.mangaupdates.com/releases.html";
    //https://regex101.com/r/8vGpn3/1
    $regex = '/<div class=.col-6 pbreak.*href=.(.*)..ti.*Info.>(.*)<\/a.*\n.*c.(.*)<.*\n.*href=.(.*)..ti.*Info.>(.*)<\/a.*/m';

    return getList($link, $regex, 5);
}
/*
    Reading List
        [0](manga1)
            [0] Title
        [1](manga2)
    but convert into 1d array for simplicity
    Reading List
        [0] Title
        [1] Title2
*/
function getMyMangaList()
{
    $regex = '/title=.Series Info.><u>(.*)<\/u>/m';

    $tempList = getList(MYLIST, $regex, 1);
    $list = array();

    foreach($tempList as $temp)
        array_push($list, $temp[0]);

    return $list;
}
/*
    Checks if each entry in $releases is in $reading. If so, adds it to a new array.
    Returns that array
*/
function compareLists($releases, $reading)
{
    $rssMangaList = array();

    foreach($releases as $releasedChapter)
    {
        if(isChapterBeingRead($releasedChapter[1], $reading))
            array_push($rssMangaList, $releasedChapter);
    }
    
    return $rssMangaList;
}
/*
    Returns true if given $chapterName is in 1d array $reading
*/ 
function isChapterBeingRead($chapterName,$reading)
{
    //CHANGE THIS TO BINARY SEARCH LATER
    foreach($reading as $manga)
    {
        if ($manga == $chapterName)
            return true;
    }

    return false;
}
/*
    Prints given $list in 2d array form of Release List into an RSS xml file
*/
function printRSS($list)
{
    //Add a <image> here later. https://www.rssboard.org/rss-specification
    echo "<?xml version='1.0' encoding='UTF-8' ?>
    <rss version='2.0'>
        <channel>
            <title> Baka-Updates RSS </title>
            <link> https://www.mangaupdates.com/releases.html </link>
            <description> RSS for Releases but only with manga on your Reading list </description>";

    //[0] Link to manga , [1] Title, [2] chapter number, [3] Link to Group, [4] Group
    foreach($list as $manga)
    {
        $mangaLink = $manga[0];
        $title = $manga[1];
        $chapters = $manga[2];
        $groupLink = $manga[3];
        $group = $manga[4];

        //Is there a better guid than this?

        echo 
        "<item>
            <title> Chapter(s) $chapters of $title </title>
            <link> $mangaLink </link>
            <description><![CDATA[ Check out the new chapter(s) of <a href='$mangaLink'>$title</a> translated by <a href='$groupLink'>$group</a> ]]></description>
            <guid>$title $chapters $group </guid>
        </item>";
    }

    echo "</channel></rss>";
}

loadSettings();

$releases = getLatestReleaseList();
$reading = getMyMangaList();
$rssMangaList = compareLists($releases,$reading);
printRSS($rssMangaList);

// echo "<pre>";
// print_r($releases);
// print_r($reading);
// print_r($rssMangaList);
// echo "</pre>";
?>
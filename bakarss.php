<?php
function loadSettings()
{
    $xml = simplexml_load_file("settings.xml");
    define("MYLIST",$xml->reading);
}
/*
    Goes the given $url and performs the given $reg as a regular expression.
    $x denotes the amount of matches from the regex to include in the return array.
    Returns a 2D array.
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
            [0] Manga ID , [1] Manga name, [2] chapter number, [3] Group ID, [4] Group name
        [1](manga2)
*/
function getLatestReleaseList()
{
    $link = "https://www.mangaupdates.com/releases.html";
    $regex = '/<div class=.col-6 pbreak.*href=.https:\/\/www.mangaupdates.com\/series.html\?id=(.*)..ti.*Info.>(.*)<\/a.*\n.*c.(.*)<.*\n.*href=.https:\/\/www.mangaupdates.com\/groups.html\?id=(.*)..ti.*Info.>(.*)<\/a.*/m';

    return getList($link, $regex, 5);
}
/*
    Reading List
        [0](manga1)
            [0] Manga ID [1] Manga name
        [1](manga2)
*/
function getMyMangaList()
{
    $regex = '/<td class=.text pl .*><a href=.https:\/\/www.mangaupdates.com\/series.html\?id=(.*). title=.Series Info.><u>(.*)<\/u>/m';

    $temp= getList(MYLIST, $regex, 2);
    
    sort($temp);

    return $temp;
}
/*
    Checks if each entry in $releases is in $reading by comparing manga ids. If so, adds it to a new array.
    Returns that array
*/
function compareLists($releases, $reading)
{
    $rssMangaList = array();

    foreach($releases as $releasedChapter)
    {
        if(isChapterBeingRead($releasedChapter[0], $reading))
            array_push($rssMangaList, $releasedChapter);
    }
    
    return $rssMangaList;
}
/*
    Returns true if given $chapterName is in 1d array $reading
*/ 
function isChapterBeingRead($chapter,$reading)
{
    //Binary Search
    $low = 0;
    $high = count($reading)-1;

    while ($low <= $high)
    {
        $mid = ($low + $high) /2;

        if ($chapter > $reading[$mid][0])
            $low = $mid +1;
        else if($chapter < $reading[$mid][0])
            $high = $mid -1;
        else
            return true;
    }

    return false;
}
/*
    Prints given $list in 2d array form of Release List into an RSS xml file
*/
function printRSS($list)
{
    //Add a <image> here later? Not needed for QuiteRSS. https://www.rssboard.org/rss-specification
    echo "<?xml version='1.0' encoding='UTF-8' ?>
    <rss version='2.0'>
        <channel>
            <title> Baka-Updates RSS </title>
            <link> https://www.mangaupdates.com/releases.html </link>
            <description> RSS for Releases but only with manga on your Reading list </description>";

    //[0] Manga ID , [1] Manga name, [2] chapter number, [3] Group ID, [4] Group
    foreach($list as $manga)
    {
        $mangaID = $manga[0];
        $title = $manga[1];
        $chapters = $manga[2];
        $groupID = $manga[3];
        $group = $manga[4];

        echo 
        "<item>
            <title> Chapter(s) $title - Chapter(s) $chapters </title>
            <link> https://www.mangaupdates.com/series.html?id=$mangaID </link>
            <description><![CDATA[Group: <a href='https://www.mangaupdates.com/groups.html?id=$groupID'>$group</a> ]]></description>
            <guid>$mangaID $groupID $chapters  </guid>
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
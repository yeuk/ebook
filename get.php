<?php

error_reporting(E_ALL ^ E_NOTICE);
define('HTML_PATH', './cache');
@mkdir(HTML_PATH);

$urlExtArr = [
    '24history.html',
    'acient_art.html',
    'acient_biji.html',
    'acient_dao.html',
    'acient_fo.html',
    'acient_history.html',
    'acient_leishu.html',
    'acient_medicine.html',
    'acient_poem.html',
    'acient_rucang.html',
    'acient_set.html',
    'acient_yi.html',
    'acient_zalun.html',
    'acient_zhuzi_baijia.html',
    'biography.html',
    'century.html',
    'child_growth.html',
    'children.html',
    'china.html',
    'china_classic.html',
    'classic.html',
    'college.html',
    'computer.html',
    'court_story.html',
    'culture.html',
    'english.html',
    'english_novel.html',
    'financial_investment.html',
    'foreign_literature.html',
    'geography.html',
    'guanchang_shangzhan.html',
    'guoxue.html',
    'ibooks.html',
    'learning.html',
    'life.html',
    'literature.html',
    'love.html',
    'medicine.html',
    'military.html',
    'modern_novel.html',
    'natural.html',
    'phychology.html',
    'social.html',
    'success.html',
    'swordmen_novel.html',
    'thought.html',
    'total1.html',
    'total2.html',
    'work.html',
    'youth.html',
];

$argv[1]
    ? getHtml($urlExtArr)
    : getContent($urlExtArr);

function getContent($urlExtArr)
{
    $urlPre = 'https://universsky.github.io/';
    $downloadUrlPre = 'https://github.com/universsky/universsky.github.io/raw/master/epubjs/books/';

    $tableTmpl = '<table> <tr> <td class="td1"><a href="{{$downloadUrl}}" target="_blank"><svg aria-hidden="true" class="octicon octicon-file-text" height="16" version="1.1" viewBox="0 0 12 16" width="12"><path d="M6 5H2V4h4v1zM2 8h7V7H2v1zm0 2h7V9H2v1zm0 2h7v-1H2v1zm10-7.5V14c0 .55-.45 1-1 1H1c-.55 0-1-.45-1-1V2c0-.55.45-1 1-1h7.5L12 4.5zM11 5L8 2H1v12h10V5z"></path></svg></a></td> <td><a href="{{$bookUrl}}" target="_blank">{{$bookName}}</a></td> </tr> </table>';


    $htmlTmpl = '<!DOCTYPE html> <html> <head> <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><title>王伟龙的ebook - {{$title}}</title> <style> body {position:absolute;left:20%;} table {background: #fff; border-radius: 2px; border-spacing: 0; border-collapse: collapse; border: 1px solid #dfe2e5; width: 80%; float: left; margin: 5px 0 0 5px; } td {padding: 6px 3px; line-height: 20px; } .td1 {width: 40px;} svg {width: 17px; padding-right: 2px; padding-left: 10px; color: rgba(3, 47, 98, 0.55); fill: currentColor;} </style> </head> <body> <div>{{$html}}</div></body> </html>';

    $br = "\r\n";
    echo "共计：", count($urlExtArr), '类', $br;
    $success = $error = 0;

    $all = '';
    $unknow = 1;
    foreach ($urlExtArr as $urlExt) {
        $url = $urlPre . $urlExt;
        $content = @file_get_contents(HTML_PATH . "/$urlExt");
        if ($content) {

            $html = '';
            preg_match('/<h4>(.*?)<\/h4>/i', $content, $match);
            $categroy = $match[1] ?: '未知分类' . $unknow++;
            $dir = "./$categroy";
            @mkdir($dir);
            $html .= "<h4>$categroy</h4>";
            $cate[] = $categroy;

            preg_match_all('/<a(.*?)href\=[\'"](epubjs\/html\/(\d+_.*?)\.html)[\'"]\>(.*?)<\/a>/i', $content, $matchs);

            foreach ($matchs[2] as $k => $bookUrl) {
                $bookUrl = $urlPre . $bookUrl;
                $downloadUrl = $downloadUrlPre . $matchs[3][$k] . '.epub';
                $bookName = str_replace(['光剑免费图书馆', ' -', '- '], [], $matchs[3][$k]);
                $bookName = trim($bookName);
                $all .= "$categroy / $bookName.epub  ：$bookUrl $br";
                $html .= str_replace(
                    ['{{$downloadUrl}}', '{{$bookUrl}}', '{{$bookName}}'],
                    [$downloadUrl, $bookUrl, $bookName],
                    $tableTmpl
                );
            }

            $result = str_replace(
                ['{{$title}}', '{{$html}}'],
                [$categroy, $html],
                $htmlTmpl
            );
            file_put_contents("$dir/index.html", $result);
            $success++;
            echo $urlExt, " $categroy success", $br;
        } else {
            $error++;
            echo $urlExt, ' error', $br;
        }
    }

    $html = "<h3>电子书收藏</h3>&nbsp;<h4><a href='all_book.txt'>所有目录</a></h4>";
    foreach ($cate as $categroy) {
        $html .= str_replace(
            ['{{$downloadUrl}}', '{{$bookUrl}}', '{{$bookName}}'],
            ["$categroy/", "$categroy/", $categroy],
            $tableTmpl
        );
    }
    $result = str_replace(
        ['{{$title}}', '{{$html}}'],
        ['电子书收藏', $html],
        $htmlTmpl
    );
    file_put_contents("./index.html", $result);
    file_put_contents("./all_book.txt", $all);

    echo "成功$success, 失败$error";
}

function getHtml($urlExtArr)
{
    $urlPre = 'https://universsky.github.io/';

    $br = "\r\n";
    echo "共计：", count($urlExtArr), '类', $br;
    $success = $error = 0;
    foreach ($urlExtArr as $urlExt) {
        $url = $urlPre . $urlExt;
        $content = @file_get_contents($url);
        if ($content) {
            @file_put_contents(HTML_PATH . "/$urlExt", $content);
            $success++;
            echo $urlExt, ' success', $br;
        } else {
            $error++;
            echo $urlExt, ' error', $br;
        }
    }

    echo "成功$success, 失败$error";
}



<?php
$p='docs/Rate It Platform SRS V2.0 (3).docx';
$z=new ZipArchive();
$z->open($p);
$xml=$z->getFromName('word/document.xml');
$z->close();
$text=preg_replace('/<[^>]+>/u',' ',$xml);
$text=html_entity_decode($text, ENT_QUOTES|ENT_XML1,'UTF-8');
$text=preg_replace('/\s+/u',' ',$text);
echo mb_substr($text,0,5000,'UTF-8');

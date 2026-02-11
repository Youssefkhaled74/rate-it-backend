<?php
$p='docs/Rate It Platform SRS V2.0 (3).docx';
$z=new ZipArchive();
if($z->open($p)!==true){echo "open failed\n"; exit(1);} 
echo "num=".$z->numFiles."\n";
for($i=0;$i<min(20,$z->numFiles);$i++){
  echo $z->getNameIndex($i)."\n";
}
$z->close();

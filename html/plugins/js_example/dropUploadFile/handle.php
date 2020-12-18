<?php

if(!empty($_FILES['imgFile'])){
	move_uploaded_file($_FILES['imgFile']['tmp_name'],$_FILES['imgFile']['name']);	
	echo '这是图片';

}
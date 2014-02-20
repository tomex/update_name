<?php
//設定
$screen_name = ""; //TwitterID(ScreenName)で@抜き
$consumer_key = ""; // Consumer keyの値
$consumer_secret = ""; // Consumer secretの値
$access_token = ""; // Access Tokenの値
$access_token_secret = ""; // Access Token Secretの値
$template = "@{screen_name} {name}に変更されました。"; //update_name受信後返信しない場合は空欄。「{name}」で変更後の名前に置き換え。「{screen_name}」でリプライを送ってきた人のIDに置き換え。
$error = "@{screen_name} 20文字以内で指定して下さい。"; //update_name受信後update_nameが20文字以上の場合にエラーを吐きます。その時に返信しない場合は空欄。「{name}」で変更後の名前に置き換え。「{screen_name}」でリプライを送ってきた人のIDに置き換え。
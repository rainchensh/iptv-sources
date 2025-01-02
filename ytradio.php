<?php
/***********食用方法******************
*随机收听电台 XXX.php?id=rand&type= 或 XXX.php?id=&type= 或 XXX.php
*随机收听江苏电台 XXX.php?id=rand&type=jiangsu 或 XXX.php?id=&type=jiangsu 或 XXX.php?type=jiangsu
(其他城市，用$city数组中定义的城市名替换即可，如上海：shanghai；山西：shanxi_1)
*指定电台ID收听  如：上海东方广播电台,XXX.php?id=371&type=shanghai
*电台ID可通过列表来获取：
随机获取一个城市的电台列表：XXX.php?id=list&type= 或XXX.php?id=list
获取指定城市(如：四川)的电台列表：XXX.php?id=list&type=sichuan
(其他城市，用$city数组中定义的城市名替换即可，如湖南：hunan；陕西：shanxi_2)
*************************************/
header('Content-Type: text/html;charset=UTF-8');
$id = isset($_GET['id'])?$_GET['id']:'';
$header=array(
"equipmentid: 0000",
"platformcode: WEB",
'equipmentsource: WEB',
'providercode: 25010',
'version: 4.0.0',
"user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.198 Safari/537.36",
    );
if($id=='list'||$id==''||$id=='rand') {
   $type=isset($_GET['type'])?$_GET['type']:'';
   $city=array(
    'china'=>0,//中国
    'beijing'=>110000,//北京
    'hebei'=>130000,//河北
    'shanghai'=>310000,//上海
    'chongqing'=>500000,//重庆
    'henan'=>410000,//河南
    'jiangsu'=>320000,//江苏
    'guizhou'=>520000,//贵州
    'liaoning'=>210000,//辽宁
    'sichuan'=>510000,//四川
    'zhejiang'=>330000,//浙江
    'ningxia'=>640000,//宁夏
    'fujian'=>350000,//福建
    'gansu'=>620000,//甘肃
    'guangdong'=>440000,//广东
    'jiangxi'=>360000,//江西
    'shandong'=>370000,//山东
    'shanxi_1'=>140000,//山西
    'hunan'=>430000,//湖南
    'hubei'=>420000,//湖北
    'hainan'=>460000,//海南
    'jilin'=>220000,//吉林
    'heilongjiang'=>230000,//黑龙江
    'shanxi_2'=>610000,//陕西
    'neimenggu'=>150000,//内蒙古
    'guangxi'=>450000,//广西
    'yunnan'=>530000,//云南
    'anhui'=>340000,//安徽
    'qinghai'=>630000,//青海
    'xinjiang'=>650000,//新疆
    'xizang'=>540000,//西藏
    'xinjiangbingtuan'=>660000,//新疆兵团
    );
if($type!=='china'&&empty($city[$type])) {
    $type=array_keys($city)[array_rand(array_keys($city),1)];
  }
  $time=time().'123';
  $url='https://ytmsout.radio.cn/web/appBroadcast/list?categoryId=0&provinceCode='.$city[$type];
  $query=parse_url($url)['query'];
  $str=$query.'×tamp='.$time.'&key=f0fc4c668392f9f9a447e48584c214ee';
  $sign=strtoupper(md5($str));  
/ $header=array_merge($header,array("signsign","timestamptime")) ;/
  $header=array_merge($header,array("sign:$sign","timestamp:$time")) ;
  $data=get_data($url,$header);
  $info=json_decode($data);
  foreach ($info->data as $cont){
    $cid= $cont->contentId;
    $name=$cont->title;
  switch ($id) {
      case 'list':
         echo $name.',http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?id='.$cid.'&type='.$type.'</a><br>';
         break;
      default:
         /$m3u8[]=$cont->playUrlLow;/
         $m3u8[]=$cont->mp3PlayUrlHigh;
         break;
      }
    }
if($m3u8){
   $playurl=$m3u8[array_rand($m3u8,1)];
   //print_r($playurl);
   header('Location:'.$playurl);
}
}else{
$time=date('Y-m-d');
$bsurl='https://ytapi.radio.cn/ytsrv/srv/interactive/program/list';
$post='startdate='.$time.'&enddate='.$time.'&appKey=&broadCastId='.$id.'&userId=';
$info=json_decode(get_data($bsurl,$header,$post));
/$playurl=$info->playUrlHigh;/
$playurl=$info->broadcastPlayUrlHighMp3;
//print_r($playurl);
header('Location:'.$playurl);
}   
function get_data($url,$header,$post=null){
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
if(!empty($post)){
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
  }
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
$data = curl_exec($ch);
curl_close($ch);
return $data;
}
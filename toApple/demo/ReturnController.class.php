<?php
namespace Home\Controller;
use Think\Controller;
class ReturnController extends Controller {
	
	public function _empty($url){
		$this->index($url);
    }
	
    public function index($url){
		echo time();
    }

	//52mbAPI的返回值
	public function flow_api(){
	/*	
	$a = M('returnflowstatc');
	$i_data['fanhuizhi'] = date('Y-m-d H:i:s');
	$i_data['addtime'] = date('Y-m-d H:i:s');
	$a->add($i_data);
	*/
	$str = file_get_contents('php://input', 'r');
		if(empty($str) || strlen($str) =='0'){
			$jg_dm = '';
		}else{
			
			$a = M('returnflowstatc');
			$i_data['fanhuizhi'] = $str;
			$i_data['addtime'] = date('Y-m-d H:i:s');
			$a->add($i_data);
			$jg_dm = '1';
			
			//处理开始 - 根据充值结果来发送不同的短信，成功失败都要更新数据
				$s = M('flowsalelogs');
				##(1) 更新数据【成功//失败】 并 (2)更新成功（有数据）发送短信
				##(1)
				$rarray = json_decode($str,true);
				foreach($rarray as $k => $v){
					
					$liushuihao = $v['order_id'];
					$s_jg = $s->where(" fsl_salesorder = '".$liushuihao."' and fsl_state = '0' ")->find();
					if($s_jg){
						##(2) 
						$cz_jg = false;
						if($v['errcode']=='0'){						
							$s_udata['fsl_salesok'] = '0';
							$cz_jg = true;
						}else{
							$s_udata['fsl_salesok'] = '-1';
						}
						$s_udata['fsl_updatedate'] = date('Y-m-d H:i:s');
						$s_udata['fsl_state'] = 1;
						$s_up = $s->where(" fsl_salesorder = '".$liushuihao."' and fsl_state = '0' ")->save($s_udata);
						if($s_up){
							##(3)
							if($cz_jg){
								if($s_jg['fsl_otherinfo']=='buy'){
									$neirong = '尊敬的用户，您的帐户增加了全国流量'.$s_jg['fsl_fc_mb'].'M，立即生效，如有任何疑问，请致电客服。';
								}else{
									$neirong = '尊敬的用户您好，您通过52MB.COM所得的'.$s_jg['fsl_fc_mb'].'M流量叠加包已经生效，当月有效；如有疑问请联系客服';
									
								}
								//只有移动才有必要发短信
								if($s_jg['fsl_msuplier']=='2'){
									gy_sendmobile_x($s_jg['fsl_mobile'],$neirong,$s_jg['fsl_act_id'],'1');
								}
								//变更为充值成功
								$this->wslstate($liushuihao,'1',$s_jg['fsl_otherinfo']);
							}else{
								//gy_sendmobile_x($mobile,$content,$act_id,$leixing)
								//变更为充值失败
								$this->wslstate($liushuihao,'-2',$s_jg['fsl_otherinfo']);
							}
							
						}
					}
				}
			//处理结束
		}
			
		echo $jg_dm;
	}
	
	//移动的返回值
	public function yd_jieguo(){
	/*	
	$a = M('returnflowstatc');
	$i_data['fanhuizhi'] = date('Y-m-d H:i:s');
	$i_data['addtime'] = date('Y-m-d H:i:s');
	$a->add($i_data);
	*/
		if(empty($_POST)){
			$jg_dm = '-9999';
			$jg_nr = '非法请求';
		}else{
			
			$postStr = $_POST; //获取POST数据  
			foreach($postStr as $key => $value){
				$xx = $key.$value;
			}
			$xx = all_trim($xx);		
			
			$a = M('returnflowstatc');
			$i_data['fanhuizhi'] = $xx;
			$i_data['addtime'] = date('Y-m-d H:i:s');
			$a->add($i_data);
			$jg_dm = '0000';
			$jg_nr = '';
			
			//处理开始 - 根据充值结果来发送不同的短信，成功失败都要更新数据
				##(1) 更新数据【成功//失败】 并 (2)更新成功（有数据）发送短信
				##(1)
				$liushuihao = all_fengexml($xx,'orderId');
				$s = M('flowsalelogs');
				$s_jg = $s->where(" fsl_salesorder = '".$liushuihao."' and fsl_state = '0' ")->find();
				if($s_jg){
					##(2) 
					$cz_jg = false;
					if(all_fengexml($xx,'result')=='0000'){						
						$s_udata['fsl_salesok'] = '0';
						$cz_jg = true;
					}else{
						$s_udata['fsl_salesok'] = '-1';
					}
					$s_udata['fsl_updatedate'] = date('Y-m-d H:i:s');
					$s_udata['fsl_state'] = 1;
					$s_up = $s->where(" fsl_salesorder = '".$liushuihao."' and fsl_state = '0' ")->save($s_udata);
					if($s_up){
						##(3)
						if($cz_jg){
							if($s_jg['fsl_otherinfo']=='buy'){
								$neirong = '尊敬的用户，您的帐户增加了全国流量'.$s_jg['fsl_fc_mb'].'M，立即生效，如有任何疑问，请致电客服。';
							}else{
								$neirong = '尊敬的用户您好，您通过52MB.COM所得的'.$s_jg['fsl_fc_mb'].'M流量叠加包已经生效，当月有效；如有疑问请联系客服';
							}
							gy_sendmobile_x(all_fengexml($xx,'mobile'),$neirong,$s_jg['fsl_act_id'],'1');
							//变更为充值成功
							$this->wslstate($liushuihao,'1',$s_jg['fsl_otherinfo']);
						}else{
							//gy_sendmobile_x($mobile,$content,$act_id,$leixing)
							//变更为充值失败
							$this->wslstate($liushuihao,'-2',$s_jg['fsl_otherinfo']);
						}
						
					}
				}
			//处理结束
		}
		
		$data='<response>
				<result>'.$jg_dm.'</result>
				<desc>'.$jg_nr.'</desc>
			</response>';
			
		echo $data;
	}
	
	
	//联通的返回值
	public function lt_jieguo(){
	/*	
	$a = M('returnflowstatc');
	$i_data['fanhuizhi'] = date('Y-m-d H:i:s');
	$i_data['addtime'] = date('Y-m-d H:i:s');
	$a->add($i_data);
	*/
		$all_REQUEST = json_encode($_REQUEST);
		$rtarray = json_decode($all_REQUEST,true);
				
		if(empty($rtarray['transNo'])){
			$jg_dm = 'error';
			$jg_nr = '10010';
		}else{
						
			$a = M('returnflowstatc');
			$i_data['fanhuizhi'] = $all_REQUEST;
			$i_data['addtime'] = date('Y-m-d H:i:s');
			$a->add($i_data);
			$jg_dm = 'ok';
			$jg_nr = '10010';
			
			//处理开始 - 根据充值结果来发送不同的短信，成功失败都要更新数据
				##(1) 更新数据【成功//失败】 并 (2)更新成功（有数据）发送短信
				##(1)
				$liushuihao = $rtarray['transNo'];
				$s = M('flowsalelogs');
				$s_jg = $s->where(" fsl_salesorder = '".$liushuihao."' and fsl_state = '0' ")->find();
				if($s_jg){
					##(2) 
					$cz_jg = false;
					if($rtarray['respCode']=='0000'){						
						$s_udata['fsl_salesok'] = '0';
						$cz_jg = true;
					}else{
						$s_udata['fsl_salesok'] = '-1';
					}
					$s_udata['fsl_updatedate'] = date('Y-m-d H:i:s');
					$s_udata['fsl_state'] = 1;
					$s_up = $s->where(" fsl_salesorder = '".$liushuihao."' and fsl_state = '0' ")->save($s_udata);
					if($s_up){
						##(3)
						if($cz_jg){
							/*
							$neirong = '尊敬的用户您好，您通过52MB.COM所得的'.$s_jg['fsl_fc_mb'].'M流量叠加包已经生效，当月有效；如有疑问请联系客服';
							gy_sendmobile_o($s_jg['fsl_mobile'],$neirong,$s_jg['fsl_act_id'],'1');
							*/
							//变更充值成功
							$this->wslstate($liushuihao,'1',$s_jg['fsl_otherinfo']);
						}else{
							//gy_sendmobile_x($mobile,$content,$act_id,$leixing)
							//变理为充值失败
							$this->wslstate($liushuihao,'-2',$s_jg['fsl_otherinfo']);
						}
						
					}
				}
			//处理结束
		}
		
		$data= $jg_dm.' of '.$jg_nr;
			
		echo $data;
	}
	
	//电信的返回值
	public function dx_jieguo(){
	/*	
	$a = M('returnflowstatc');
	$i_data['fanhuizhi'] = date('Y-m-d H:i:s');
	$i_data['addtime'] = date('Y-m-d H:i:s');
	$a->add($i_data);
	*/
		$get_data = file_get_contents("php://input");
		if(empty($get_data)){
			$jg_dm = 'error';
			$jg_nr = '10001';
		}else{
			
			
			$a = M('returnflowstatc');
			$i_data['fanhuizhi'] = $get_data;
			$i_data['addtime'] = date('Y-m-d H:i:s');
			$a->add($i_data);
			$jg_dm = 'ok';
			$jg_nr = '10001';
			
			//处理开始 - 根据充值结果来发送不同的短信，成功失败都要更新数据
				##(1) 更新数据【成功//失败】 并 (2)更新成功（有数据）发送短信
				##(1)
					$get_data = str_replace('}"','}',str_replace('"{','{',$get_data));
					$rtarray = json_decode($get_data,true);
					
				$liushuihao = $rtarray['request_no'];
				$s = M('flowsalelogs');
				$s_jg = $s->where(" fsl_salesorder = '".$liushuihao."' and fsl_state = '0' ")->find();
				if($s_jg){
					##(2) 
					$cz_jg = false;
					if($rtarray['result_code']=='00000'){						
						$s_udata['fsl_salesok'] = '0';
						$cz_jg = true;
					}else{
						$s_udata['fsl_salesok'] = '-1';
					}
					$s_udata['fsl_updatedate'] = date('Y-m-d H:i:s');
					$s_udata['fsl_state'] = 1;
					$s_up = $s->where(" fsl_salesorder = '".$liushuihao."' and fsl_state = '0' ")->save($s_udata);
					if($s_up){
						##(3)
						if($cz_jg){
							/*
							$neirong = '尊敬的用户您好，您通过52MB.COM所得的'.$s_jg['fsl_fc_mb'].'M流量叠加包已经生效，当月有效；如有疑问请联系客服';
							gy_sendmobile_o($s_jg['fsl_mobile'],$neirong,$s_jg['fsl_act_id'],'1');
							*/
							$this->wslstate($liushuihao,'1',$s_jg['fsl_otherinfo']);
						}else{
							//gy_sendmobile_x($mobile,$content,$act_id,$leixing)
							$this->wslstate($liushuihao,'-2',$s_jg['fsl_otherinfo']);
						}
						
					}
				}
			//处理结束
		}
		
		$data= $jg_dm.' of '.$jg_nr;
			
		echo $data;
	}
	
	
	private function wslstate($orderid="1234567890",$zhi="1",$isbuy="null"){
		if($isbuy=="buy"){
			#检测销售记录表
			$s_wsl = M('wxshoplogs');
			$s_f = $s_wsl->where(" wsl_orderid = '".$orderid."' ")->field('id,wsl_act_id,wsl_money')->find();
			if($zhi=='1'){
				$s_wsl->where('id = '.$s_f['id'])->save(array('wsl_state'=>'2','wsl_ticheng'=>'0'));
			}else{
				$s_wsl->where('id = '.$s_f['id'])->save(array('wsl_state'=>'-1','wsl_ticheng'=>'0'));
			}
			
			//echo '<br />'.$s_f['wsl_act_id'].'<br />'.var_dump(strpos("==51=34==","=".$s_f['wsl_act_id']."="));
			#成功，且不是系统用户的 话给用户发短信（1）获取用户信息,（2）给相应用户发短信
			if($zhi=='1' && strpos("==51=34==","=".$s_f['wsl_act_id']."=") == '0'){
				  #（1）
				  $ua = M('activities')->where(" id = '".$s_f['wsl_act_id']."' ")->field('act_userid,act_dianpuinfo')->find();
				  $ui = M('member')->where('id = '.$ua['act_userid'])->field('user,mobile')->find();
				
				  //var_dump($s_f);var_dump($u_id);var_dump($ui);exit;
				  #（2）
				  if(strlen($ui['mobile']) == '11'){
				  	  #$mbile = '15210030529';
				  	  $mbile = $ui['mobile'];
				  	  $dzxx = $ua['act_dianpuinfo'];
				  	  $dzxx = json_decode($dzxx,true);
				  	  $dzxx = $dzxx['dz'];
				  	  if(strlen($dzxx)<1){
					  	$dzxx = '尊敬的';
					  }
				  	  
		              $send_code = $dzxx.'店主，您的店铺又进账 '.sprintf("%.2f",$s_f['wsl_money']/100).' 大洋，快去看看吧！店铺链接：http://t.cn/R4ytQNg 退订回复T';
		              /*
		              //echo $send_code; exit;
		              $jg = file_get_contents("http://114.215.202.188:8081/SmsAndMms/mg?Sn=52mb&Pwd=bjhh52mb88&mobile=".$mbile."&content=".$send_code);
		              $jg = explode($jg,'>');
		              $send_state = str_replace('</int>','',$jg[1]);
		              $map['ml_mobile'] = $mbile;
		              $map['ml_code'] = $send_code;
		              $map['ml_actid'] = 21;  //购买充值成功
		              $map['ml_type'] = 4;    //短信验证类型
		              $map['ml_state'] = 1; //本条记录状态
		              $map['ml_adddate'] = date('Y-m-d H:i:s', time());
		              $res = M('mobilelogs')->add($map);
				  	*/
					  gy_sendmobile_x($mbile,$send_code,'21','4');
				  }
			}
			#充值成功短结束
			#充值失败后开始退款开始
			else{
				
				$pid = 'tptGy2Ot4z7c7YWKJ5MDW1VfqoRwyE5KMvIfoEjjfPXiKgmX';
				$key = 'yZYgNAr5B3ZH8Wh6vNe4TRHWfb6Y2JemmNj5kbzmHAQ01vnJCc2FSunRNcFPBScn';
				$orderid = $orderid; // date('YmdHis').rand('10','99');
				$noncestr = all_createNonceStr(36);
				$sign = md5(sha1('noncestr='.$noncestr.'&pid='.$pid.'&key='.$key.'&data='.base64_encode($orderid)));
				
				$data = array('data'=>base64_encode($orderid),'hash'=>$noncestr,'sign'=>$sign);
				
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, 'http://wx.52mb.com/index.php/WeiXin/Accept/accept');
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
				// curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
				curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
				curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, FALSE);
				$this->result = curl_exec($ch);
				curl_close($ch);
				unset($ch);
			}
			#充值失败后开始退款结束
		}
	}
	
	
}

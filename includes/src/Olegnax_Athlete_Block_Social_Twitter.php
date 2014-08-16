<?php
require_once( Mage::getBaseDir('code') . "/local/Olegnax/Athlete/OAuth/twitteroauth.php");

/**
 * Twitter block
 *
 */
class Olegnax_Athlete_Block_Social_Twitter extends Mage_Core_Block_Template
{

	protected $_cacheKeyArray;

	protected function _construct()
	{
		$this->addData(array(
			'cache_lifetime' => 3600,
			'cache_tags'     => array('athlete_twitter'),
		));
	}

	public function getCacheKeyInfo()
	{
		if (NULL === $this->_cacheKeyArray)
		{
			$this->_cacheKeyArray = array(
				Mage::app()->getStore()->getId(),
				Mage::getDesign()->getPackageName(),
				Mage::getDesign()->getTheme('template'),
				$this->getTwitterName(),
				$this->getTweetsNum(),
				$this->getTemplate(),
			);
		}
		return $this->_cacheKeyArray;
	}

	public function getTwitterName()
	{
		return Mage::helper('athlete')->getCfg('social/twitter');
	}

	public function getTweetsNum()
	{
		return Mage::helper('athlete')->getCfg('social/tweets_num');
	}

	public function getTweets()
	{
		$config = Mage::helper('athlete')->getCfg('social');
		$connection = new TwitterOAuth($config['consumerkey'], $config['consumersecret'], $config['accesstoken'], $config['accesstokensecret']);
		$tweets = $connection->get("https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=".$config['twitter']."&count=".$config['tweets_num']);
		if ( empty($tweets->errors) ) {
			foreach ($tweets as $k => $v) {
				$tweets[$k]->text = $this->twitterStatusUrlConverter($v->text);
				$tweets[$k]->created_at = $this->convertDate(strtotime($v->created_at));
			}
		}
		return $tweets;
	}

	public function convertDate($date)
	{
		$stf = 0;
		$cur_time = time();
		$diff = $cur_time - $date;
		$phrase = array('second','minute','hour','day','week','month','year','decade');
		$length = array(1,60,3600,86400,604800,2630880,31570560,315705600);
		for($i =sizeof($length)-1; ($i>=0) &&(($no =  $diff/$length[$i])<=1); $i--); if($i<0) $i=0; $_time = $cur_time  -($diff%$length[$i]);
		$no = floor($no); if($no < 1) $phrase[$i] .='s'; $value=sprintf("%d %s ",$no,$phrase[$i]);
        if(($stf == 1) &&($i>= 1) &&(($cur_time-$_time)>0)) $value .= time_ago($_time);
	    return $value.' ago ';
	}

	/**
	 *
	 * twitterStatusUrlConverter
	 *
	 * To convert links on a twitter status to a clickable url. Also convert @ to follow link, and # to search
	 *
	 * @author: Mardix - http://mardix.wordpress.com, http://www.givemebeats.net
	 * @date: March 16 2009
	 * @license: LGPL (I don't care, it's free lol)
	 *
	 * @param string : the status
	 * @param bool : true|false, allow target _blank
	 * @param int : to truncate a link to max length
	 * @return String
	 *
	 * */
	public function twitterStatusUrlConverter($status,$targetBlank=true,$linkMaxLen=250){

		// The target
		$target=$targetBlank ? " target=\"_blank\" " : "";

		// convert link to url
		$status = preg_replace("/((http:\/\/|https:\/\/)[^ )
]+)/e", "'<a href=\"$1\" title=\"$1\"  $target >'. ((strlen('$1')>=$linkMaxLen ? substr('$1',0,$linkMaxLen).'...':'$1')).'</a>'", $status);

		// convert @ to follow
		$status = preg_replace("/(@([_a-z0-9\-]+))/i","<a href=\"http://twitter.com/$2\" title=\"Follow $2\" $target >$1</a>",$status);

		// convert # to search
		$status = preg_replace("/(#([_a-z0-9\-]+))/i","<a href=\"http://twitter.com/search?q=%23$2\" title=\"Search $1\" $target >$1</a>",$status);

		// return the status
		return $status;
	}


}
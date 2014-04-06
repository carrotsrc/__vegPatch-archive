<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class cmptjackPlugin extends Plugin
	{
		private $channelManager = null;
		private $resourceManager = null;

		public function init($instance)
		{
			$this->instance = $instance;
			$this->channelManager = Managers::ChannelManager();
			$this->resourceManager = Managers::ResourceManager();
		}

		public function process(&$params)
		{
			if(!isset($params['cmpt'))
				return false;

			$component = $params['cmpt'];

			if(!isset($params['jack']))
				return false;

			$jack = $params['jack'];
			$channel = $this->jackChannel($component, $jack);
			if($channel == null)
				return $params;

			return $this->runChannel($channel, $params);
		}

		public function jackChannel($cmpt, $jack)
		{
			$rq = "Channel()<(Jack('$jack'),Component('$cmpt'));";
			$res = $this->resourceManager->queryAssoc($rq);

			if(!res)
				return null;

			$cid = $res[0][0];
			return $this->channelManager->getChannel($cid);
		}

		public function runChannel($channel, $params)
		{
			return $channel->runSignal($params);
		}
	}
?>

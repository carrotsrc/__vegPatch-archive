<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
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

		public function process(&$signal)
		{
			if(!isset($signal['cmpt'))
				return false;

			$component = $signal['cmpt'];

			if(!isset($signal['jack']))
				return false;

			$jack = $signal['jack'];
			$channel = $this->jackChannel($component, $jack);
			if($channel == null)
				return $signal;

			return $this->runChannel($channel, $signal);
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

		public function runChannel($channel, $signal)
		{
			return $channel->runSignal($signal);
		}
	}
?>

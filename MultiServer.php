<?php

/*
__PocketMine Plugin__
name=MultiServer
description=Run more than 4 servers in your network
version=0.1
author=shoghicp
class=MultiServer
apiversion=5,6
*/

/*

Small Changelog
===============

0.1:
- PocketMine-MP Alpha_1.2.2 release

*/
		
class MultiServer implements Plugin{

	private $api;
	private $config;
	public function __construct(ServerAPI $api, $server = false){
		$this->api = $api;
	}
	
	public function init(){
		$this->config = new Config($this->api->plugin->configPath($this)."config.yml", CONFIG_YAML, array(
			"servers" => array(),
		));
		$this->api->console->register("multiadd", "Adds a server host / port to the ping forwarding list.", array($this, "command"));
		$this->api->addHandler("server.noauthpacket", array($this, "packetDispatcher"), 50);
	}
	
	public function packetDispatcher(&$packet, $event){
		if($event !== "server.noauthpacket"){
			return;
		}
		switch($packet["pid"]){
			case 0x02: //Ping External
				foreach($this->config->get("servers") as $server){
					ServerAPI::request()->send(0x1d, array(
						$packet["data"][0],
						Utils::readLong("\x31\x33\x70\x00\x31\x33\x70\x00"),
						RAKNET_MAGIC,
						$packet["ip"].";".$packet["port"],
					), false, $server["ip"], $server["port"]);
				}
				break;
			case 0x1d: //Ping Internal
				if($packet["data"][1] !== Utils::readLong("\x31\x33\x70\x00\x31\x33\x70\x00")){
					break;
				}
				$data = explode(";", $packet["data"][3]);
				ServerAPI::request()->packetHandler(array(
					"pid" => 0x02,
					"data" => array(
						0 => $data[0],
						1 => RAKNET_MAGIC,
					),
					"raw" => "",
					"ip" => $data[0],
					"port" => (int) $data[1]
				));
				return false;
		}
	}
	
	public function onCommand(CommandSender $sender, Command $cmd, $label, array $args){
		$output = "";
		switch($cmd){
			case "multiadd":
				if($issuer !== "console"){					
					$output .= "Please run this command on the console.\n";
					break;
				}
				if(!isset($params[0])){
					$output = "Usage: /multiadd <port> [host]\n";
					break;
				}
				$port = (int) $params[0];
				$server = "127.0.0.1";
				if(isset($params[1])){
					$server = $params[1];
				}
				$servers = $this->config->get("servers");
				$servers[md5($port.$server)] = array("server" => $server, "port" => $port);
				$this->config->set("servers", $servers);
				$this->config->save();
				break;
		}
		return $output;
	}
	
	public function __destruct(){
	}

}

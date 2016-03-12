# MultiServer <em>for PocketMine-MP</em>

Notice: This uses the api of a really old pocketmine version and is made for minecraft version ~0.6.1!

Break the limit of 4 servers in [PocketMine-MP](https://github.com/shoghicp/PocketMine-MP) using this plugin!


## Installation
- Drop the correct MultiServer version into the PocketMine's `plugins/` folder.
- Restart the server. The plugin will be loaded
- You've to set up a head server so it forwards the info to other servers. It must be on the 19132-19135 port range.
 - After the plugin has been installed, configure it using `/multiadd <port> [server]`.
 - If the server is on the same computer, omit the _server_ parameter.
 - Done!

- _Note: Internal servers created with this method won't need prot forwarding, only the main one._

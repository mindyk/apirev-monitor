apirev-monitor
==============

apilib revision monitor


## How to use ###
so far only the cli entry point is rdy to use. Use <code>php cli/main.php</code> from your cli in the project root dir to get a list of command options

## Commands ##
*collect-data* will fetch the logs from the servers   
*process-data* will process ALL logs found in tmp/ so feel free to clean up before processing

## Config ##
dont forget to edit the /etc/config.dist.json with your values  
<code>
{
	"api_server_url" : "###API_SERVER_URL###",
	"dry-run": "1",
	"db_path": "etc/apirev.db"
}
<code>

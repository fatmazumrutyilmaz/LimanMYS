<?php 
    function index(){
        return view('index');
    }

    function tab1(){
        return respond(runCommand("hostname"),200);
    }

    function trustedServers(){
        $allData = runCommand(sudo() . "samba-tool domain trust list 2>&1");
        $allDataList = explode("\n", $allData);

        $data=[];
        foreach($allDataList as $item){
            if($item){
                $itemInfos = explode("[", $item);
                $data[] = [
                    "type" => substr($itemInfos[1], 0, strpos($itemInfos[1], "]")),
                    "transitive" => substr($itemInfos[2], 0, strpos($itemInfos[2], "]")),
                    "direction" => substr($itemInfos[3], 0, strpos($itemInfos[3], "]")),
                    "name" => substr($itemInfos[4], 0, strpos($itemInfos[4], "]"))
                ];
            }
        }                

        return view('table', [
            "value" => $data,
            "title" => ["Names of Servers", "*hidden*", "*hidden*", "*hidden*"],
            "display" => ["name", "type:type", "transitive:transitive", "direction:direction"],
            "onclick" => "showTrustedServerDetailsModal",
            "menu" => [

                "Delete" => [
                    "target" => "showDeleteTrustedServerModal",
                    "icon" => "fas fa-trash-alt"
                ],

            ],
        ]);
    }

    function destroyTrustRelation(){
        $name = request("name");
        $password = request("password");
        $output = runCommand(sudo() . "samba-tool domain trust delete " . $name .
                            " -U administrator@" . $name .
                            " --password=" . $password);
        return respond("Trust relation with " . $name . " was destroyed", 200);
    }

    function createTrustRelation(){
        $domainName = request("newDomainName");
        $ipAddr = request("newIpAddr");
        $type = request("newType");
        $direction = request("newDirection");
        $createLocation = request("newCreateLocation");
        $username = request("newUsername");
        $password = request("password");

        if(!($domainName && $ipAddr && $type && $direction && $createLocation && $username && $password))
            return respond("Please fill all fields!", 202);

        runCommand(sudo() . "samba-tool domain trust create " . $domainName .
                    " --type=" . $type . " --direction=" . $direction .
                    " --create-location=" . $createLocation . " -U " . $username .
                    "@" . $domainName . " --password=" . $password);
        return respond("Trust relation with " . $domainName . " has been created", 200);
    }

    function groups(){        
        $allData = runCommand("cat /etc/group");
        $allDataList = explode("\n", $allData);

        $message = "Info is not valid!";
        $data = [];
        for($i=0; $i<count($allDataList); $i++){
            $item = $allDataList[$i];
            $itemList = explode(":", $item);

            $nameItem = $itemList[0];
            if($nameItem != ""){
                $infoItem = $itemList[3];
                if($infoItem == ""){
                    $data[] = [
                        "name" => $nameItem,
                        "info" => $message
                    ];
                }
                else {
                    $data[] = [
                        "name" => $nameItem,
                        "info" => $infoItem
                    ];
                }
            }
        }

        return view('table', [
            "value" => $data,
            "title" => ["Users", "Info" ],
            "display" => ["name", "info"],
        ]);
    }

    function createFile(){
        $fileName = request("fileName");
        $content = request("content");
        runCommand('echo "' . $content . '" > /home/fatmazumrutyilmaz/Masaüstü/' . $fileName);
        return respond($fileName, 200);
    }

    //$username = extensionDb('smbUserName');
?>

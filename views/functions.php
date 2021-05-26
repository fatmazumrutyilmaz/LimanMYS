<?php 
    function index(){
        return view('index');
    }

    function tab1(){
        return respond(runCommand("hostname"),200);
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
?>

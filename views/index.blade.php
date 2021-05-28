@component('modal-component',[
        "id" => "createFileModal",
        "title" => "Dosya Oluştur",
        "footer" => [
            "text" => "Oluştur",
            "class" => "btn-success",
            "onclick" => "createFile()"
        ]
    ])
    @include('inputs', [
        "inputs" => [
            "Dosya Adı" => "filename:text:Dosya adı",
            "Icerik" => "content:text:Icerik"
        ]
    ])
@endcomponent

@component('modal-component',[
        "id" => "createTrustRelationModal",
        "title" => "Create Trust Relation",
        "footer" => [
            "text" => "Create",
            "class" => "btn-success",
            "onclick" => "showCreateTrustRelationModal()"
        ]
    ])
    @include('inputs', [
        "inputs" => [
            "Domain Name" => "newDomainName:text",
            "IP Address" => "newIpAddr:text",
            "Type" => "newType:text",
            "Direction" => "newDirection:text",
            "Create Location" => "newCreateLocation:text",
            "Username" => "newUsername:text"
        ]
    ])
@endcomponent

@component('modal-component',[
        "id" => "trustedServerDetailsModal",
        "title" => "Details",
        "footer" => [
            "text" => "Close",
            "class" => "btn-success",
            "onclick" => "closeTrustedServerDetailsModal()"
        ]
    ])
@endcomponent

@component('modal-component',[
        "id" => "deleteTrustedServerModal",
        "title" => "Warning",
        "footer" => [
            "text" => "Cancel",
            "class" => "btn-success",
            "onclick" => "closeTrustedServerDetailsModal()"
        ],
        "footer" => [
            "text" => "Delete",
            "class" => "btn-success",
            "onclick" => "destroyTrustRelation()"
        ]
    ])
@endcomponent

<ul class="nav nav-tabs" role="tablist" style="margin-bottom: 15px;">
    <li class="nav-item">
        <a class="nav-link active"  onclick="tab1()" href="#tab1" data-toggle="tab">Server Name</a>
    </li>
    <li class="nav-item">
        <a class="nav-link "  onclick="trustedServers()" href="#trustRelation" data-toggle="tab">Trusted Servers</a>
    </li>
    <li class="nav-item">
        <a class="nav-link "  onclick="groups()" href="#groups" data-toggle="tab">Groups</a>
    </li>
    <li class="nav-item">
        <a class="nav-link "  onclick="showFileModal()" href="#showFileModal" data-toggle="tab">Add File</a>
    </li>
</ul>

<div class="tab-content">
    <div id="tab1" class="tab-pane active">
    </div>

    <div id="trustRelation" class="tab-pane">
        <button class="btn btn-success mb-2" id="createButton" onclick="showCreateTrustRelationModal()" type="button">Create</button>    
        <div id="trustedServers">
        </div>
    </div>

    <div id="groups" class="tab-pane">
    </div>
</div>

<script>
    var domainName = "";

    if(location.hash === ""){
        tab1();
    }

    function tab1(){
        var form = new FormData();
        request("{{API('tab1')}}", form, function(response) {
            message = JSON.parse(response)["message"];
            $('#tab1').html(message);
        }, function(error) {
            $('#tab1').html("Hata oluştu");
        });
    }

    function trustedServers(){
        var form = new FormData();
        request("{{API('trustedServers')}}", form, function(response) {
            $('#trustedServers').html(response).find('table').DataTable({
            bFilter: true,
            "language" : {
                url : "/turkce.json"
            }
          });;
        }, function(error) {
            $('#trustedServers').html("Hata oluştu");
        });
        
    }

    function showTrustedServerDetailsModal(line){
        var name = line.querySelector("#name").innerHTML;
        var type = line.querySelector("#type").innerHTML;
        var transitive = line.querySelector("#transitive").innerHTML;
        var direction = line.querySelector("#direction").innerHTML;
        console.log(name);
        if(name)
            $('#trustedServerDetailsModal h4.modal-title').html("Details");
        $('#trustedServerDetailsModal').find('.modal-body').html(
            "Name".bold() + "</br>" + name + "</br>" + "</br>" +
            "Type".bold() + "</br>" + type + "</br>" + "</br>" +
            "Transitive".bold() + "</br>" + transitive + "</br>" + "</br>" +
            "Direction".bold() + "</br>" + direction + "</br>" + "</br>"
        );
        $('#trustedServerDetailsModal').modal("show");
    }

    function closeTrustedServerDetailsModal(){
        $('#trustedServerDetailsModal').modal("hide");
    }

    function showDeleteTrustedServerModal(line){
        let name = line.querySelector("#name").innerHTML;
        domainName = name;
        $('#deleteTrustedServerModal').find('.modal-body').html(
            "Trust relation with \"".bold() + name.bold() + "\" will destroy. Do you really want to continue?".bold());
        $('#deleteTrustedServerModal').modal("show");
    }

    function closeDeleteTrustedServerModal(){
        $('#deleteTrustedServerModal').modal("hide");
    }

    function destroyTrustRelation(line){
        var form = new FormData();
        form.append("name", domainName);
        request(API('destroyTrustRelation'), form, function(response) {
            message = JSON.parse(response)["message"];
            showSwal(message, 'success', 3000);
            closeDeleteTrustedServerModal();
        }, function(error) {
            showSwal(error.message, 'error', 3000);
        });
    }

    function showCreateTrustRelationModal(){
        $('#createTrustRelationModal').modal("show");
    }

    function createTrustRelation(){
        var form = new FormData();
        form.append("newDomainName", $('#createTrustRelationModal').find('input[name=newDomainName]').val().toLowerCase());
        form.append("newIpAddr", $('#createTrustRelationModal').find('input[name=newIpAddr]').val());
        form.append("newType", $('#createTrustRelationModal').find('input[name=newType]').val().toLowerCase());
        form.append("newDirection", $('#createTrustRelationModal').find('input[name=newDirection]').val().toLowerCase());
        form.append("newCreateLocation", $('#createTrustRelationModal').find('input[name=newCreateLocation]').val().toLowerCase());
        form.append("newUsername", $('#createTrustRelationModal').find('input[name=newUsername]').val());

        request(API('createTrustRelation'), form, function(response) {
            message = JSON.parse(response)["message"];
            showSwal(message, 'success', 10000);
            $('#createTrustRelationModal').modal("hide");
        }, function(error) {
            showSwal(error.message, 'error', 3000);
        });
    }

    function groups(){
        var form = new FormData();
        request("{{API('groups')}}", form, function(response) {
            $('#groups').html(response).find('table').DataTable({
            bFilter: true,
            "language" : {
                url : "/turkce.json"
            }
          });;
        }, function(error) {
            $('#groups').html("Hata oluştu");
        });
    }

    function showFileModal(){
        $('#createFileModal').modal("show");
        var serverName = document.getElementById("tab1").innerText;
        if(serverName)
            $('#createFileModal h4.modal-title').html(`Dosya Oluştur (${serverName})`);
    }

    function createFile(){
        var form = new FormData();
        let fileName = $('#createFileModal').find('input[name=filename]').val();
        let content = $('#createFileModal').find('input[name=content]').val();
        form.append("fileName", fileName);
        form.append("content", content);

        request(API('createFile'), form, function(response) {
            message = JSON.parse(response)["message"];
            showSwal(message, 'success', 3000);
            $('#createFileModal').modal("hide");
        }, function(error) {
            showSwal(error.message, 'error', 3000);
        });
    }
</script>
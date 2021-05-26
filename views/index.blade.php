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

<ul class="nav nav-tabs" role="tablist" style="margin-bottom: 15px;">
    <li class="nav-item">
        <a class="nav-link active"  onclick="tab1()" href="#tab1" data-toggle="tab">Server Name</a>
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

    <div id="groups" class="tab-pane">
    </div>
</div>

<script>
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
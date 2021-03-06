@extends('layouts.default')
@section('style')
<style>
  @media (min-width: 1280px) {
    #btnSimpan{margin-left: 15px;}
  }
</style>
@stop
@section('content')
	{{--start breadcrumb and header page--}}
	<header class="content-header">
		<div class="page-header">
			<h1 class="text-info"><span>Master Jenis Barang</span></h1>
		</div>
		<ul class="breadcrumb breadcrumb-inline">
			<li><a href="#" title="Ke dashboard">Dashboard</a></li>
			<li><i class="aweso-angle-right"></i></li>
			<li><a href="#" title="Klien">Master Jenis Barang</a></li>
			<li><i class="aweso-angle-right"></i></li>
			<li class="active"><span class="text-judul">Data</span></li>
		</ul>
		<ul class="content-action pull-right">
			<li>
				<div class="controls" id="form-cari">
					<div class="input-append input-append-inline">
						<input id="cari" class="span2" title="Isikan data yang ingin dicari lalu tekan [Enter]" placeholder="Cari : nama barang" type="text"  onfocus='this.select()'/>
						<span class="add-on"><i class="aweso-search"></i></span>
					</div>
				</div>
			</li>
			<li>
				<button id="btnTambah" onclick="TombolTambah()" title="Tambah Data. Shortcut Ctrl+Alt+T " class="btn bg-cyan" type="button"><i class="aweso-plus-sign-alt"></i></button>
			</li>
		</ul>
	</header>
	{{--breadcrumb and header page end--}}

	{{--start content--}}
	<article class="content-page">
		<div class="main-page">
			<div class="content-inner">
				{{--alert notify--}}
				<div id="alert-notify" class="alert alert-success" style="display: none;"></div>
				<div class="widget border-cyan" id="widget">
					<div class="widget-header bg-cyan">
						<h4 class="widget-title"><i class="aweso-list"></i><span id="load" class="text-judul">Data</span></h4>
						<div class="widget-action">
							{{--info paging--}}
							<span id="infopage" style="padding-right:10px"></span>
							{{--navigasi mundur--}}
							<button id="mundur" title="Ke halaman sebelumnya" class="btn bg-silver" onclick="Mundur()" disabled="disabled">
								<i class="aweso-chevron-left"></i>
							</button>
							{{--navigasi maju--}}
							<button id="maju" title="Ke halaman berikutnya" class="btn bg-silver" onclick="Maju()" disabled="disabled">
								<i class="aweso-chevron-right"></i>
							</button>
							<button data-toggle="collapse" data-collapse="#widget" class="btn color-cyan">
								<i class="aweso-chevron-sign-up" data-toggle-icon="aweso-chevron-sign-up  aweso-chevron-sign-down"></i>
							</button>
						</div>
					</div>
					<div class="widget-content">
						{{--list data--}}
						<div id="data" class="tab-content">
							<table class="table table-striped">
								<thead>
									<th>Kode Jenis</th>
									<th>Jenis Barang</th>
									<th>Keterangan</th>
									<th>Aksi</th>
								</thead>
								<tbody id="datalist"></tbody>
							</table>
						</div>
						{{--form tambah--}}
						<div id="form1" style="display: none ;">
							{{ Form::open(array('onsubmit' => 'return false', 'id' => 'myForm', 'class' => 'form-horizontal')) }}
							{{ Form::hidden('id', Input::old('id'),array('id' => 'id','name' => 'id')) }}
              {{ Form::hidden('', Input::old(''),array('id' => 'last_page')) }}
              {{ Form::hidden('', Input::old(''),array('id' => 'cmd')) }}
              {{ Form::hidden('', Input::old(''),array('id' => 'current_page')) }}
								{{--nama_jenis--}}
								<div class="control-group">
									{{ Form::label('nama_jenis', 'Jenis Barang', array('class' => 'control-label')) }}
									<div class="controls controls-row">
										{{ Form::text('nama_jenis', Input::old('nama_jenis'), array('class' => 'span4'))}}
									</div>
								</div>

								{{--keterangan--}}
								<div class="control-group">
									{{ Form::label('ket', 'Keterangan', array('class' => 'control-label')) }}
									<div class="controls controls-row">
										{{ Form::textarea('ket', Input::old('ket'), array('class' => 'span4','rows' => '2'))}}
									</div>
								</div>
								
								{{--form submit--}}
								<div class="form-actions bg-silver">
									{{ Form::submit('Simpan', array('class' => 'btn btn-primary','id' =>'btnSimpan')) }}
                	{{ Form::button('Batal', array('class' => 'btn btn-default','onclick'=>'TombolBatal()','id' =>'btnBatal')) }}
								</div>
							{{ Form::close() }}
						</div>
					</div>
				</div>
			</div>
		</div>
	</article>
	{{--content end--}}
@stop

@section('scripts')
  <script type="text/javascript">
  	// onReady
    $(document).ready(function(){
      	$("title").text("Master Jenis Barang | App Penjualan")
      	$("#navside #li-master-jenis").attr("class", "active");
    	$("#navbar-collapse #li-master-jenis").attr("class", "active");
    	$("#cmd").val('add');
        TampilData(1);
    	// ketika ada event enter untuk pencarian
    	$("#cari").keypress(function(event) {
            if (event.which == 13) {
                event.preventDefault();
                TampilData(1);
            }
        });
        
    });

    // navigasi maju ke halaman selanjutnya
    function Maju(page){
        $("#maju").attr('disabled', 'disabled');
      $("#mundur").attr('disabled', 'disabled');
        var last_page = $("#last_page").val();
        var current_page = $("#current_page").val();
        page = page+eval(1)
        if(page == last_page){
          $("#maju").attr('disabled', 'disabled');
        }  else {
          $("#maju").removeAttr('disabled');
        }
        TampilData(page);  
      }

    // navigasi mundur ke halaman sebelumnya
    function Mundur(page){
        $("#maju").attr('disabled', 'disabled');
      $("#mundur").attr('disabled', 'disabled');
        page = page-eval(1)
        if(page == 1){
          $("#mundur").attr('disabled', 'disabled');
          $("#maju").removeAttr('disabled');
        } else {
          $("#mundur").removeAttr('disabled');
        }
        TampilData(page);  
    }

    // Ajax menampilkan data
    function TampilData(page) {
        var nama = $("#cari").val();
          $.ajax({
          type: "post",
          url: "{{ URL::to('jenis-barang/read?page="+page+"') }}",
          cache: false,       
          data:'nama='+ nama,
          success: function(data){
            var obj = json2array(data)
            $("#infopage").text(obj[4]+" - "+obj[5]+" dari "+obj[0])
            
            $("#current_page").val(obj[2])
            $("#last_page").val(obj[3])

            $("#maju").attr('onclick','Maju('+obj[2]+')')
            $("#mundur").attr('onclick','Mundur('+obj[2]+')')

            if(obj[2] == obj[3]){
              $("#maju").attr('disabled', 'disabled');
            } else {
              $("#maju").removeAttr('disabled');
            }
            if(obj[2] > 1){
              $("#mundur").removeAttr('disabled'); 
            }

            $("#datalist").html("")
            if(obj[6].length == 0){
                $("#datalist").append("<tr><td colspan='4'>Data kosong.</td></tr>");
              } else {
                $.each(obj[6], function(index, val) {
                  $("#datalist").append(
                  	"<tr><td>" + val.id 
                    + "</td><td>" + val.nama_jenis
                    + "</td><td>" + val.ket 
                    + "</td><td><a class='btn bt-group' onclick='EditData("+ val.id +")'><i class='aweso-edit'></i></a></tr>"
                  )
                });
              }
            $("#cari").focus();
          },
          error: function(data){}
        });
      }

      // Ajax simpan data
      function SimpanData(){
        $.ajax({
          url: "{{ Route('master-jenis-barang.store') }}",
          type: 'POST',
          data: $("#myForm").serialize(),
        })
        .done(function(data) {
            $("#alert-notify").show();
            $("#alert-notify").text("");
            var data = json2array(data)
                if (data[0] == "Warning"){
                  for (var i = 1; i < 2; i++) {
                    $("#alert-notify").removeClass('alert-success');
                    $("#alert-notify").addClass('alert-danger');
                    $('#alert-notify').append("<ul style='margin-bottom: 0px;'>"+ data[i] +"</ul>");
                  };
                  } else {
                    $("#datalist").html("");
                    $("#alert-notify").removeClass('alert-danger');
                    $("#alert-notify").addClass('alert-success');
                    $('#alert-notify').append("<ul style='margin-bottom: 0px;'>"+ data[1] +"</ul>");
                    TombolBatal();
              };

        })
        .fail(function(data) {

        })
        .always(function(data) {
          
        });
                   
      }

      function EditData(id) {
          $.ajax({
          type: "get",
          url: "{{ URL::to('master-jenis-barang/"+id+"/edit') }}",
          cache: false,       
          data:'id='+id,
          success: function(data){
            $("#cmd").val('update');
            TombolTambah();
            $("#btnSimpan").attr({
              onclick: '',
              value: 'Update'
            });
            var val = json2array(data)
            $("#id").val(val[0]);
            $("#nama_jenis").val(val[1]);
            $("#ket").val(val[2]);
            
            
          },
          error: function(data){            
            
          }
        });
      }

      function UpdateData(){
        var id = $("#id").val()
        $.ajax({
          url: "{{ URL::to('master-jenis-barang/"+id+"') }}",
          type: 'post',
          data: '_method=put&'+$("#myForm").serialize(),
        })
        .done(function(data) {
            $("#alert-notify").show();
            $("#alert-notify").text("");
            var data = json2array(data)
                if (data[0] == "Warning"){
                  for (var i = 1; i < 5; i++) {
                    $("#alert-notify").removeClass('alert-success');
                    $("#alert-notify").addClass('alert-danger');
                    $('#alert-notify').append("<ul tyle='margin-bottom: 0px;'>"+ data[i] +"</ul>");
                  };
                  } else {
                    $("#datalist").html("");
                    $("#alert-notify").removeClass('alert-danger');
                    $("#alert-notify").addClass('alert-success');
                    $('#alert-notify').append("<ul style='margin-bottom: 0px;'>"+ data[1] +"</ul>");
                    TombolBatal();
              };


        })
        .fail(function(data) {

        })
        .always(function(data) {
          
        });
                   
      }

      function HapusData(id) {
        $.confirm({
        title: "Konfirmasi Hapus",
        text: "Yakin hapus data?",
        confirm: function(button) {

            $.ajax({
            type: "post",
            url: "{{ URL::to('master-barang/"+id+"') }}",
            cache: false,       
            data:'id='+id,
            success: function(data){
              var data = json2array(data)
              $("#alert-notify").text("");    
              $("#alert-notify").show();
              $("#datalist").html("");
              $("#alert-notify").removeClass('alert-danger');
              $("#alert-notify").addClass('alert-success');
              $('#alert-notify').append("<ul>"+ data[1] +"</ul>");
              $("#alert-notify").fadeOut(1000);
              TampilData($("#current_page").val());
            },
            error: function(data){            
              
            }
          });
        },
        confirmButton: "Ya, hapus",
        cancelButton: "Batal",
        post: true
    });
    }

      // tombol Tambah
      function TombolTambah(){
        $("#data").hide();
        $("#form1").show();
        $(".text-judul").text("Tambah");
        $("#form-cari").hide();
        $("#mundur").hide();
        $("#maju").hide();
        $("#infopage").hide();
        $("#btnTambah").hide();
        $("#btnFilter").hide();
        $("#btnLaporan").hide();
        $("#nama_jenis").focus();
      }

      // tombol batal
      function TombolBatal(){
      	Kosongkan();
        TampilData($("#current_page").val());
        $("#cmd").val('add');
        $("#form1").hide();
        $("#btnSimpan").attr({
          onclick: '',
          value: 'Simpan'
        });
        $("#data").show();
        $("#mundur").show();
        $("#maju").show();
        $(".text-judul").text("Data");
        $("#infopage").show();
        $("#form-cari").show();
        $("#btnTambah").show();
        $("#btnFilter").show();
        $("#btnLaporan").show();
        $("span.text-error").text("");
      	$("#alert-notify").fadeOut(1000);
      	$("#cari").focus();
      }

      // kosongkan form
      function Kosongkan(){
        $("#id").val("");
        $("#nama_jenis").val("");
      	$("#ket").val("");
      }

    //Validasi
	$(function() {
	    $.validator.setDefaults({}),
	    $("#myForm").validate({
	        submitHandler: function(form) {
                var cmd = $("#cmd").val();
                switch (cmd) {
                    case "add": SimpanData(); break;
                    case "update": UpdateData(); break;
                    default: ""
                }
	        	
	        },
	        errorElement: "small",
	        errorPlacement: function(e, t) {
	            var n = t.parent();
	            n.is(".controls") ? e.appendTo(n) : e.appendTo(n.parent()),
	            e.addClass("help-inline")
	        },
	        rules: {
	            // nama_jenis: "required",
	        },
	        messages: {
	            nama_jenis:"<span class='text-error'>jenis barang wajib diisi.</span>",
	        }
	    })
	});

  </script>

  <!-- {{ HTML::script('public/js/app/master-barang.js') }} -->
@stop

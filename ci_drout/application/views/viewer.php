<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>


<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>



<!-- Button to open the modal -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#document-modal" data-filename="example.docx">View Document</button>

<!-- Modal -->
<div class="modal fade" id="document-modal" tabindex="-1" role="dialog" aria-labelledby="document-modal-label">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="document-modal-label">Document Preview</h4>
      </div>
      <div class="modal-body">
        <iframe id="document-iframe" src="" frameborder="0" width="100%" height="800px"></iframe>
      </div>
    </div>
  </div>
</div>

<script>

  // When the modal is shown, load the document in the iframe
  $('#document-modal').on('show.bs.modal', function(event) {
    var button = $(event.relatedTarget);
    var filename = button.data('filename');
    $('#document-iframe').attr('src', '<?php echo base_url("viewer") ?>?filename=' + encodeURIComponent(filename));
  });

</script>
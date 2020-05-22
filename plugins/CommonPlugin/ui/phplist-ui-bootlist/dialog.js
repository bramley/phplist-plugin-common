<script>
$(document).ready(function(){
    $(".dialog").click(function() {
        $("#dialog").html(
            '<div class="modal fade" id="mymodal" tabindex="-1" role="dialog" aria-labelledby="mymodalLabel" aria-hidden="true"><div class="modal-dialog modal-lg"><button type="button" class="close externo" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">x</span></button><div class="modal-content well col-lg-12"></div></div></div>'
        );
        $("#dialog .modal-content").load(this.href);
        $("#dialog #mymodal").modal('show');
        closedialog = function () {
            $("#dialog #mymodal").modal('hide');
        };
        return false;
    });
})
</script>

<script>
$(document).ready(function(){
    $(".dialog").click(function() {
        $("#dialog").dialog();
        $("#dialog").load(this.href);
        closedialog = function () {
            $("#dialog").dialog('close');
        };
        return false;
    });
})
</script>

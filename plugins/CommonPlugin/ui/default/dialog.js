<script>
$(document).ready(function(){
    $(".dialog").click(function() {
        $("#dialog").dialog();
        $("#dialog").load(this.href);
        return false;
    });
})
</script>

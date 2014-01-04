<div class="disc_bg">
<div class="container">

<ul class="disc_cont disc_tags_cont" style="margin-top:10px">
<?php $this->load->view("body/discovery/subs/sub_tags",array("cols"=>5,"showuser"=>true)); ?>
</ul>

<div class="clear"></div>

<?php if(!$this->uri->segment(2)){?>
<div class="scrollman" id="loadmoretrig"></div>
<div class="tagsloading">Loading more tags... please wait..</div>
<div class="notagstoload">no more tags to load!</div>
<script>
$tp=1;
</script>
<?php }?>

</div>
</div>

<?php


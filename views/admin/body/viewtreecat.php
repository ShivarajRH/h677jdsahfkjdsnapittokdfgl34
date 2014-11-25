<div id="wrapper"><span class="label"><?php echo $parentname; ?></span>

<?php if($subcategory){ ?>

  <div class="branch lv1">
  <?php foreach($subcategory as $subval ){ 
    if(count($subcategory)==1){ $clsname = "entry sole"; } else { $clsname = "entry";}
  	?>
    <div class="<?php echo $clsname; ?>"><span class="label"><a href="<?php echo $subval['id']; ?>"><?php echo $subval['name']; ?></a></span>
    
    <?php if($childcat[$subval['id']]){ ?>
      <div class="branch lv2">
      	<?php foreach($childcat[$subval['id']] as $grandval ){ ?>
        <div class="entry"><span class="label"><a href="<?php echo $grandval['id']; ?>"><?php echo $grandval['name']; ?></a></span>
        </div>
      <?php }?>
      </div>
      <?php }?>
    </div>
    <?php }?>
   
  </div>
  <?php }?>
				
</div>
      <style>
      *, *:before, *:after {
  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
  box-sizing: border-box;
}


#wrapper {
  position: relative;
}

.branch {
  position: relative;
  margin-left: 250px;
}
.branch:before {
  content: "";
  width: 50px;
  border-top: 2px solid #eee9dc;
  position: absolute;
  left: -100px;
  top: 50%;
  margin-top: 1px;
}

.entry {
  position: relative;
  min-height: 60px;
}
.entry:before {
  content: "";
  height: 100%;
  border-left: 2px solid #eee9dc;
  position: absolute;
  left: -50px;
}
.entry:after {
  content: "";
  width: 50px;
  border-top: 2px solid #eee9dc;
  position: absolute;
  left: -50px;
  top: 50%;
  margin-top: 1px;
}
.entry:first-child:before {
  width: 10px;
  height: 50%;
  top: 50%;
  margin-top: 2px;
  border-radius: 10px 0 0 0;
}
.entry:first-child:after {
  height: 10px;
  border-radius: 10px 0 0 0;
}
.entry:last-child:before {
  width: 10px;
  height: 50%;
  border-radius: 0 0 0 10px;
}
.entry:last-child:after {
  height: 10px;
  border-top: none;
  border-bottom: 2px solid #eee9dc;
  border-radius: 0 0 0 10px;
  margin-top: -9px;
}
.entry.sole:before {
  display: none;
}
.entry.sole:after {
  width: 50px;
  height: 0;
  margin-top: 1px;
  border-radius: 0;
}

.label {
  display: block;
  min-width: 150px;
  padding: 5px 10px;
  line-height: 20px;
  text-align: center;
  border: 2px solid #eee9dc;
  border-radius: 5px;
  position: absolute;
  left: 0;
  top: 50%;
  margin-top: -15px;
}

      </style>
     
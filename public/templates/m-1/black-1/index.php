<?php
/**
 *
 * id: black-1
 * base: m-1 
 * title: Black
 * 
 */

?>


<div class="cc-pu-bg m-1 black-1"></div>
<article class="pop-up-cc m-1 black-1  <?php echo $template_options['size']['size'];?>">
	<div class="modal-inner">
		<?php $views_control = get_post_meta($id,'_chch_pfc_show_only_once',true); ?>
		<a class="cc-pu-close chch-pfc-close" data-modalId="<?php echo $id; ?>" data-views-control="yes" data-expires-control="<?php echo $views_control ?>">  <i class="fa fa-times"></i> </a>  
		
		<?php $content = $template_options['contents']; ?>
		<div class="cc-pu-header-section"> 
			<h2><?php echo $content['header'];?></h2>
		</div>
		
		<div class="cc-pu-subheader-section"> 
			<h3><?php echo $content['subheader'];?></h3>
		</div>
		
		<div class="cc-pu-content-section cc-pu-content-styles"> 
			<?php echo wpautop($content['content']);?> 
		</div>
		
		<ul class="cc-pu-buttons"> 
			<?php $btn_left = $template_options['left_button']; ?>
			
			<?php if(!empty($btn_left['url']) || is_admin()):?>
			<li class="cc-pu-btn-wrapper">
				<a href="<?php echo $btn_left['url']; ?>" class="cc-pu-btn-left">
					<span><?php echo $btn_left['header'];?></span>
					<small><?php echo $btn_left['subheader'];?></small>
				</a>
			</li>
			<?php endif;?>
			
			<?php $btn_rgt = $template_options['right_button']; ?>
			
			<?php if(!empty($btn_rgt['url']) || is_admin()):?>
			<li class="cc-pu-btn-right-wrapper">
				<a href="<?php echo $btn_rgt['url']; ?>" class="cc-pu-btn-right">
					<span><?php echo $btn_rgt['header'];?></span>
					<small><?php echo $btn_rgt['subheader'];?></small> 
				</a>
			</li>
			<?php endif;?>
		</ul> 
	</div>
</article>

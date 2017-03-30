<?php

$target = $this->config( 'admin/jsonadm/url/target' );
$cntl = $this->config( 'admin/jsonadm/url/controller', 'jsonapi' );
$action = $this->config( 'admin/jsonadm/url/action', 'index' );
$config = $this->config( 'admin/jsonadm/url/config', [] );

$resources = [];
foreach( $this->get( 'resources', [] ) as $resource ) {
	$resources[$resource] = $this->url( $target, $cntl, $action, array( 'resource' => $resource, 'id' => '' ), [], $config );
}

?>
{
	"meta": {
		"prefix": <?php echo json_encode( $this->get( 'prefix' ) ); ?>,
		"resources": <?php echo json_encode( $resources ); ?>

	}

	<?php if( isset( $this->errors ) ) : ?>

		, "errors": <?php echo json_encode( $this->errors, JSON_PRETTY_PRINT ); ?>

	<?php endif; ?>
}
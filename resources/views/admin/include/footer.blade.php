	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
  	<script type="text/javascript">
  		$SIDEBAR_MENU = $('#sitebar-nav');
  		$SIDEBAR_MENU.find('a').on('click', function(ev) {
		  console.log('clicked - sidebar_menu');
	   //      var $li = $(this).parent();

	   //      if ($li.is('.active')) {
	   //          $li.removeClass('active active-sm');
	   //          $('ul:first', $li).slideUp(function() {
	   //              setContentHeight();
	   //          });
	   //      } else {
	   //          // prevent closing menu if we are on child menu
	   //          if (!$li.parent().is('.child_menu')) {
	   //              $SIDEBAR_MENU.find('li').removeClass('active active-sm');
	   //              $SIDEBAR_MENU.find('li ul').slideUp();
	   //          }else
	   //          {
				// 	if ( $BODY.is( ".nav-sm" ) )
				// 	{
				// 		$SIDEBAR_MENU.find( "li" ).removeClass( "active active-sm" );
				// 		$SIDEBAR_MENU.find( "li ul" ).slideUp();
				// 	}
				// }
	   //          $li.addClass('active');

	   //          $('ul:first', $li).slideDown(function() {
	   //              setContentHeight();
	   //          });
	   //      }
	    });
  	</script>
</body>
</html>
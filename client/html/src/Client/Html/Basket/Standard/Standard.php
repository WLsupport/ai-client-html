<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Basket\Standard;


/**
 * Default implementation of standard basket HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Standard
	extends \Aimeos\Client\Html\Basket\Base
	implements \Aimeos\Client\Html\Common\Client\Factory\Iface
{
	/** client/html/basket/standard/standard/subparts
	 * List of HTML sub-clients rendered within the basket standard section
	 *
	 * The output of the frontend is composed of the code generated by the HTML
	 * clients. Each HTML client can consist of serveral (or none) sub-clients
	 * that are responsible for rendering certain sub-parts of the output. The
	 * sub-clients can contain HTML clients themselves and therefore a
	 * hierarchical tree of HTML clients is composed. Each HTML client creates
	 * the output that is placed inside the container of its parent.
	 *
	 * At first, always the HTML code generated by the parent is printed, then
	 * the HTML code of its sub-clients. The order of the HTML sub-clients
	 * determines the order of the output of these sub-clients inside the parent
	 * container. If the configured list of clients is
	 *
	 *  array( "subclient1", "subclient2" )
	 *
	 * you can easily change the order of the output by reordering the subparts:
	 *
	 *  client/html/<clients>/subparts = array( "subclient1", "subclient2" )
	 *
	 * You can also remove one or more parts if they shouldn't be rendered:
	 *
	 *  client/html/<clients>/subparts = array( "subclient1" )
	 *
	 * As the clients only generates structural HTML, the layout defined via CSS
	 * should support adding, removing or reordering content by a fluid like
	 * design.
	 *
	 * @param array List of sub-client names
	 * @since 2014.03
	 * @category Developer
	 */
	private $subPartPath = 'client/html/basket/standard/standard/subparts';
	private $subPartNames = [];
	private $view;


	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string HTML code
	 */
	public function getBody( $uid = '' )
	{
		$context = $this->getContext();
		$view = $this->getView();

		try
		{
			if( !isset( $this->view ) ) {
				$view = $this->view = $this->getObject()->addData( $view );
			}

			$html = '';
			foreach( $this->getSubClients() as $subclient ) {
				$html .= $subclient->setView( $view )->getBody( $uid );
			}
			$view->standardBody = $html;
		}
		catch( \Aimeos\Client\Html\Exception $e )
		{
			$error = array( $context->getI18n()->dt( 'client', $e->getMessage() ) );
			$view->standardErrorList = array_merge( $view->get( 'standardErrorList', [] ), $error );
		}
		catch( \Aimeos\Controller\Frontend\Exception $e )
		{
			$error = array( $context->getI18n()->dt( 'controller/frontend', $e->getMessage() ) );
			$view->standardErrorList = array_merge( $view->get( 'standardErrorList', [] ), $error );
		}
		catch( \Aimeos\MShop\Exception $e )
		{
			$error = array( $context->getI18n()->dt( 'mshop', $e->getMessage() ) );
			$view->standardErrorList = array_merge( $view->get( 'standardErrorList', [] ), $error );
		}
		catch( \Exception $e )
		{
			$error = array( $context->getI18n()->dt( 'client', 'A non-recoverable error occured' ) );
			$view->standardErrorList = array_merge( $view->get( 'standardErrorList', [] ), $error );
			$this->logException( $e );
		}

		/** client/html/basket/standard/standard/template-body
		 * Relative path to the HTML body template of the basket standard client.
		 *
		 * The template file contains the HTML code and processing instructions
		 * to generate the result shown in the body of the frontend. The
		 * configuration string is the path to the template file relative
		 * to the templates directory (usually in client/html/templates).
		 *
		 * You can overwrite the template file configuration in extensions and
		 * provide alternative templates. These alternative templates should be
		 * named like the default one but with the string "standard" replaced by
		 * an unique name. You may use the name of your project for this. If
		 * you've implemented an alternative client class as well, "standard"
		 * should be replaced by the name of the new class.
		 *
		 * @param string Relative path to the template creating code for the HTML page body
		 * @since 2014.03
		 * @category Developer
		 * @see client/html/basket/standard/standard/template-header
		 */
		$tplconf = 'client/html/basket/standard/standard/template-body';
		$default = 'basket/standard/body-standard';

		return $view->render( $view->config( $tplconf, $default ) );
	}


	/**
	 * Returns the HTML string for insertion into the header.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string|null String including HTML tags for the header on error
	 */
	public function getHeader( $uid = '' )
	{
		$view = $this->getView();

		try
		{
			if( !isset( $this->view ) ) {
				$view = $this->view = $this->getObject()->addData( $view );
			}

			$html = '';
			foreach( $this->getSubClients() as $subclient ) {
				$html .= $subclient->setView( $view )->getHeader( $uid );
			}
			$view->standardHeader = $html;
		}
		catch( \Exception $e )
		{
			$this->logException( $e );
			return '';
		}

		/** client/html/basket/standard/standard/template-header
		 * Relative path to the HTML header template of the basket standard client.
		 *
		 * The template file contains the HTML code and processing instructions
		 * to generate the HTML code that is inserted into the HTML page header
		 * of the rendered page in the frontend. The configuration string is the
		 * path to the template file relative to the templates directory (usually
		 * in client/html/templates).
		 *
		 * You can overwrite the template file configuration in extensions and
		 * provide alternative templates. These alternative templates should be
		 * named like the default one but with the string "standard" replaced by
		 * an unique name. You may use the name of your project for this. If
		 * you've implemented an alternative client class as well, "standard"
		 * should be replaced by the name of the new class.
		 *
		 * @param string Relative path to the template creating code for the HTML page head
		 * @since 2014.03
		 * @category Developer
		 * @see client/html/basket/standard/standard/template-body
		 */
		$tplconf = 'client/html/basket/standard/standard/template-header';
		$default = 'basket/standard/header-standard';

		return $view->render( $view->config( $tplconf, $default ) );
	}


	/**
	 * Returns the sub-client given by its name.
	 *
	 * @param string $type Name of the client type
	 * @param string|null $name Name of the sub-client (Default if null)
	 * @return \Aimeos\Client\Html\Iface Sub-client object
	 */
	public function getSubClient( $type, $name = null )
	{
		/** client/html/basket/standard/decorators/excludes
		 * Excludes decorators added by the "common" option from the basket standard html client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "client/html/common/decorators/default" before they are wrapped
		 * around the html client.
		 *
		 *  client/html/basket/standard/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("\Aimeos\Client\Html\Common\Decorator\*") added via
		 * "client/html/common/decorators/default" to the html client.
		 *
		 * @param array List of decorator names
		 * @since 2014.05
		 * @category Developer
		 * @see client/html/common/decorators/default
		 * @see client/html/basket/standard/decorators/global
		 * @see client/html/basket/standard/decorators/local
		 */

		/** client/html/basket/standard/decorators/global
		 * Adds a list of globally available decorators only to the basket standard html client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("\Aimeos\Client\Html\Common\Decorator\*") around the html client.
		 *
		 *  client/html/basket/standard/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\Client\Html\Common\Decorator\Decorator1" only to the html client.
		 *
		 * @param array List of decorator names
		 * @since 2014.05
		 * @category Developer
		 * @see client/html/common/decorators/default
		 * @see client/html/basket/standard/decorators/excludes
		 * @see client/html/basket/standard/decorators/local
		 */

		/** client/html/basket/standard/decorators/local
		 * Adds a list of local decorators only to the basket standard html client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("\Aimeos\Client\Html\Basket\Decorator\*") around the html client.
		 *
		 *  client/html/basket/standard/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\Client\Html\Basket\Decorator\Decorator2" only to the html client.
		 *
		 * @param array List of decorator names
		 * @since 2014.05
		 * @category Developer
		 * @see client/html/common/decorators/default
		 * @see client/html/basket/standard/decorators/excludes
		 * @see client/html/basket/standard/decorators/global
		 */

		return $this->createSubClient( 'basket/standard/' . $type, $name );
	}


	/**
	 * Sets the necessary parameter values in the view.
	 */
	public function process()
	{
		$view = $this->getView();
		$context = $this->getContext();
		$controller = \Aimeos\Controller\Frontend::create( $context, 'basket' );

		try
		{
			switch( $view->param( 'b_action' ) )
			{
				case 'add':
					$this->addProducts( $view );
					break;
				case 'coupon-delete':
					$this->deleteCoupon( $view );
					break;
				case 'delete':
					$this->deleteProducts( $view );
					break;
				default:
					$this->updateProducts( $view );
					$this->addCoupon( $view );
			}

			parent::process();

			/** client/html/basket/standard/check
			 * Alters the behavior of the product checks before continuing with the checkout
			 *
			 * By default, the product related checks are performed every time the basket
			 * is shown. They test if there are any products in the basket and execute all
			 * basket plugins that have been registered for the "check.before" and "check.after"
			 * events.
			 *
			 * Using this configuration setting, you can either disable all checks completely
			 * (0) or display a "Check" button instead of the "Checkout" button (2). In the
			 * later case, customers have to click on the "Check" button first to perform
			 * the checks and if everything is OK, the "Checkout" button will be displayed
			 * that allows the customers to continue the checkout process. If one of the
			 * checks fails, the customers have to fix the related basket item and must click
			 * on the "Check" button again before they can continue.
			 *
			 * Available values are:
			 *  0 = no product related checks
			 *  1 = checks are performed every time when the basket is displayed
			 *  2 = checks are performed only when clicking on the "check" button
			 *
			 * @param integer One of the allowed values (0, 1 or 2)
			 * @since 2016.08
			 * @category Developer
			 * @category User
			 */
			$check = (int) $view->config( 'client/html/basket/standard/check', 1 );

			switch( $check )
			{
				case 2: if( $view->param( 'b_check', 0 ) == 0 ) { break; }
				case 1: $controller->get()->check( \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT );
				default: $view->standardCheckout = true;
			}
		}
		catch( \Aimeos\Controller\Frontend\Exception $e )
		{
			$error = array( $context->getI18n()->dt( 'controller/frontend', $e->getMessage() ) );
			$view->standardErrorList = array_merge( $view->get( 'standardErrorList', [] ), $error );
		}
		catch( \Aimeos\MShop\Plugin\Provider\Exception $e )
		{
			$errors = array( $context->getI18n()->dt( 'mshop', $e->getMessage() ) );
			$errors = array_merge( $errors, $this->translatePluginErrorCodes( $e->getErrorCodes() ) );

			$view->standardErrorCodes = $e->getErrorCodes();
			$view->standardErrorList = array_merge( $view->get( 'standardErrorList', [] ), $errors );
		}
		catch( \Aimeos\MShop\Exception $e )
		{
			$error = array( $context->getI18n()->dt( 'mshop', $e->getMessage() ) );
			$view->standardErrorList = array_merge( $view->get( 'standardErrorList', [] ), $error );
		}
		catch( \Exception $e )
		{
			$error = array( $context->getI18n()->dt( 'client', 'A non-recoverable error occured' ) );
			$view->standardErrorList = array_merge( $view->get( 'standardErrorList', [] ), $error );
			$this->logException( $e );
		}

		// store updated basket after plugins updated content and have thrown an exception
		$controller->save();
	}


	/**
	 * Returns the list of sub-client names configured for the client.
	 *
	 * @return array List of HTML client names
	 */
	protected function getSubClientNames()
	{
		return $this->getContext()->getConfig()->get( $this->subPartPath, $this->subPartNames );
	}


	/**
	 * Sets the necessary parameter values in the view.
	 *
	 * @param \Aimeos\MW\View\Iface $view The view object which generates the HTML output
	 * @param array &$tags Result array for the list of tags that are associated to the output
	 * @param string|null &$expire Result variable for the expiration date of the output (null for no expiry)
	 * @return \Aimeos\MW\View\Iface Modified view object
	 */
	public function addData( \Aimeos\MW\View\Iface $view, array &$tags = [], &$expire = null )
	{
		$context = $this->getContext();
		$site = $context->getLocale()->getSiteItem()->getCode();

		if( ( $params = $context->getSession()->get( 'aimeos/catalog/detail/params/last/' . $site ) ) !== null )
		{
			$target = $view->config( 'client/html/catalog/detail/url/target' );
			$controller = $view->config( 'client/html/catalog/detail/url/controller', 'catalog' );
			$action = $view->config( 'client/html/catalog/detail/url/action', 'detail' );
			$config = $view->config( 'client/html/catalog/detail/url/config', [] );
		}
		else
		{
			$params = $context->getSession()->get( 'aimeos/catalog/lists/params/last/' . $site, [] );

			$target = $view->config( 'client/html/catalog/lists/url/target' );
			$controller = $view->config( 'client/html/catalog/lists/url/controller', 'catalog' );
			$action = $view->config( 'client/html/catalog/lists/url/action', 'list' );
			$config = $view->config( 'client/html/catalog/lists/url/config', [] );
		}

		if( empty( $params ) === false ) {
			$view->standardBackUrl = $view->url( $target, $controller, $action, $params, [], $config );
		}

		$basket = \Aimeos\Controller\Frontend::create( $this->getContext(), 'basket' )->get();

		$view->standardBasket = $basket;
		$view->standardTaxRates = $this->getTaxRates( $basket );
		$view->standardNamedTaxes = $this->getNamedTaxes( $basket );
		$view->standardCostsDelivery = $this->getCostsDelivery( $basket );
		$view->standardCostsPayment = $this->getCostsPayment( $basket );

		return parent::addData( $view, $tags, $expire );
	}


	/**
	 * Adds the coupon specified by the view parameters from the basket.
	 *
	 * @param \Aimeos\MW\View\Iface $view View object
	 */
	protected function addCoupon( \Aimeos\MW\View\Iface $view )
	{
		if( ( $coupon = $view->param( 'b_coupon' ) ) != '' )
		{
			\Aimeos\Controller\Frontend::create( $this->getContext(), 'basket' )->addCoupon( $coupon );
			$this->clearCached();
		}
	}


	/**
	 * Adds the products specified by the view parameters to the basket.
	 *
	 * @param \Aimeos\MW\View\Iface $view View object
	 */
	protected function addProducts( \Aimeos\MW\View\Iface $view )
	{
		$context = $this->getContext();
		$domains = ['attribute', 'media', 'price', 'product', 'text'];

		$basketCntl = \Aimeos\Controller\Frontend::create( $context, 'basket' );
		$productCntl = \Aimeos\Controller\Frontend::create( $context, 'product' )->uses( $domains );

		if( ( $prodid = $view->param( 'b_prodid', '' ) ) !== '' && $view->param( 'b_quantity', 0 ) > 0 )
		{
			$basketCntl->addProduct(
				$productCntl->get( $prodid ),
				$view->param( 'b_quantity', 0 ),
				$view->param( 'b_attrvarid', [] ),
				$this->getAttributeMap( $view->param( 'b_attrconfid', [] ) ),
				$view->param( 'b_attrcustid', [] ),
				$view->param( 'b_stocktype', 'default' ),
				$view->param( 'b_supplier' ),
				$view->param( 'b_siteid' )
			);
		}
		else
		{
			$list = [];
			$entries = (array) $view->param( 'b_prod', [] );

			foreach( $entries as $values )
			{
				if( isset( $values['prodid'] ) ) {
					$list[] = $values['prodid'];
				}
			}

			foreach( $entries as $values )
			{
				if( isset( $values['prodid'] ) && isset( $values['quantity'] ) && $values['quantity'] > 0 )
				{
					$basketCntl->addProduct( $productCntl->get( $values['prodid'] ),
						( isset( $values['quantity'] ) ? (int) $values['quantity'] : 0 ),
						( isset( $values['attrvarid'] ) ? array_filter( (array) $values['attrvarid'] ) : [] ),
						$this->getAttributeMap( isset( $values['attrconfid'] ) ? $values['attrconfid'] : [] ),
						( isset( $values['attrcustid'] ) ? array_filter( (array) $values['attrcustid'] ) : [] ),
						( isset( $values['stocktype'] ) ? (string) $values['stocktype'] : 'default' ),
						( isset( $values['supplier'] ) ? (string) $values['supplier'] : null ),
						( isset( $values['siteid'] ) ? (string) $values['siteid'] : null )
					);
				}
			}
		}

		$this->clearCached();
	}


	/**
	 * Removes the coupon specified by the view parameters from the basket.
	 *
	 * @param \Aimeos\MW\View\Iface $view View object
	 */
	protected function deleteCoupon( \Aimeos\MW\View\Iface $view )
	{
		if( ( $coupon = $view->param( 'b_coupon' ) ) != '' )
		{
			\Aimeos\Controller\Frontend::create( $this->getContext(), 'basket' )->deleteCoupon( $coupon );
			$this->clearCached();
		}
	}


	/**
	 * Removes the products specified by the view parameters from the basket.
	 *
	 * @param \Aimeos\MW\View\Iface $view View object
	 */
	protected function deleteProducts( \Aimeos\MW\View\Iface $view )
	{
		$controller = \Aimeos\Controller\Frontend::create( $this->getContext(), 'basket' );
		$products = (array) $view->param( 'b_position', [] );

		foreach( $products as $position ) {
			$controller->deleteProduct( $position );
		}

		$this->clearCached();
	}


	protected function getAttributeMap( array $values )
	{
		$list = [];
		$confIds = ( isset( $values['id'] ) ? array_filter( (array) $values['id'] ) : [] );
		$confQty = ( isset( $values['qty'] ) ? array_filter( (array) $values['qty'] ) : [] );

		foreach( $confIds as $idx => $id )
		{
			if( isset( $confQty[$idx] ) && $confQty[$idx] > 0 ) {
				$list[$id] = $confQty[$idx];
			}
		}

		return $list;
	}


	/**
	 * Edits the products specified by the view parameters to the basket.
	 *
	 * @param \Aimeos\MW\View\Iface $view View object
	 */
	protected function updateProducts( \Aimeos\MW\View\Iface $view )
	{
		$controller = \Aimeos\Controller\Frontend::create( $this->getContext(), 'basket' );
		$products = (array) $view->param( 'b_prod', [] );

		if( ( $position = $view->param( 'b_position', '' ) ) !== '' )
		{
			$products[] = array(
				'position' => $position,
				'quantity' => $view->param( 'b_quantity', 1 )
			);
		}

		foreach( $products as $values )
		{
			$controller->updateProduct(
				( isset( $values['position'] ) ? (int) $values['position'] : 0 ),
				( isset( $values['quantity'] ) ? (int) $values['quantity'] : 1 )
			);
		}

		$this->clearCached();
	}
}

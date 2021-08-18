<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Checkout\Standard\Address;


// Strings for translation
sprintf( 'address' );


/**
 * Default implementation of checkout address HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Standard
	extends \Aimeos\Client\Html\Common\Client\Factory\Base
	implements \Aimeos\Client\Html\Common\Client\Factory\Iface
{
	/** client/html/checkout/standard/address/subparts
	 * List of HTML sub-clients rendered within the checkout standard address section
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
	private $subPartPath = 'client/html/checkout/standard/address/subparts';

	/** client/html/checkout/standard/address/billing/name
	 * Name of the billing part used by the checkout standard address client implementation
	 *
	 * Use "Myname" if your class is named "\Aimeos\Client\Checkout\Standard\Address\Billing\Myname".
	 * The name is case-sensitive and you should avoid camel case names like "MyName".
	 *
	 * @param string Last part of the client class name
	 * @since 2014.03
	 * @category Developer
	 */

	/** client/html/checkout/standard/address/delivery/name
	 * Name of the delivery part used by the checkout standard address client implementation
	 *
	 * Use "Myname" if your class is named "\Aimeos\Client\Checkout\Standard\Address\Delivery\Myname".
	 * The name is case-sensitive and you should avoid camel case names like "MyName".
	 *
	 * @param string Last part of the client class name
	 * @since 2014.03
	 * @category Developer
	 */
	private $subPartNames = array( 'billing', 'delivery' );


	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string HTML code
	 */
	public function getBody( string $uid = '' ) : string
	{
		$view = $this->getView();
		$step = $view->get( 'standardStepActive', 'address' );
		$onepage = $view->config( 'client/html/checkout/standard/onepage', [] );

		if( $step != 'address' && !( in_array( 'address', $onepage ) && in_array( $step, $onepage ) ) ) {
			return '';
		}

		$html = '';
		foreach( $this->getSubClients() as $subclient ) {
			$html .= $subclient->setView( $view )->getBody( $uid );
		}
		$view->addressBody = $html;

		/** client/html/checkout/standard/address/template-body
		 * Relative path to the HTML body template of the checkout standard address client.
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
		 * @see client/html/checkout/standard/address/template-header
		 */
		$tplconf = 'client/html/checkout/standard/address/template-body';
		$default = 'checkout/standard/address-body-standard';

		return $view->render( $view->config( $tplconf, $default ) );
	}


	/**
	 * Returns the HTML string for insertion into the header.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string|null String including HTML tags for the header on error
	 */
	public function getHeader( string $uid = '' ) : ?string
	{
		$view = $this->getView();
		$step = $view->get( 'standardStepActive' );
		$onepage = $view->config( 'client/html/checkout/standard/onepage', [] );

		if( $step != 'address' && !( in_array( 'address', $onepage ) && in_array( $step, $onepage ) ) ) {
			return '';
		}

		return parent::getHeader( $uid );
	}


	/**
	 * Returns the sub-client given by its name.
	 *
	 * @param string $type Name of the client type
	 * @param string|null $name Name of the sub-client (Default if null)
	 * @return \Aimeos\Client\Html\Iface Sub-client object
	 */
	public function getSubClient( string $type, string $name = null ) : \Aimeos\Client\Html\Iface
	{
		/** client/html/checkout/standard/address/decorators/excludes
		 * Excludes decorators added by the "common" option from the checkout standard address html client
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
		 *  client/html/checkout/standard/address/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("\Aimeos\Client\Html\Common\Decorator\*") added via
		 * "client/html/common/decorators/default" to the html client.
		 *
		 * @param array List of decorator names
		 * @since 2015.08
		 * @category Developer
		 * @see client/html/common/decorators/default
		 * @see client/html/checkout/standard/address/decorators/global
		 * @see client/html/checkout/standard/address/decorators/local
		 */

		/** client/html/checkout/standard/address/decorators/global
		 * Adds a list of globally available decorators only to the checkout standard address html client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("\Aimeos\Client\Html\Common\Decorator\*") around the html client.
		 *
		 *  client/html/checkout/standard/address/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\Client\Html\Common\Decorator\Decorator1" only to the html client.
		 *
		 * @param array List of decorator names
		 * @since 2015.08
		 * @category Developer
		 * @see client/html/common/decorators/default
		 * @see client/html/checkout/standard/address/decorators/excludes
		 * @see client/html/checkout/standard/address/decorators/local
		 */

		/** client/html/checkout/standard/address/decorators/local
		 * Adds a list of local decorators only to the checkout standard address html client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("\Aimeos\Client\Html\Checkout\Decorator\*") around the html client.
		 *
		 *  client/html/checkout/standard/address/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\Client\Html\Checkout\Decorator\Decorator2" only to the html client.
		 *
		 * @param array List of decorator names
		 * @since 2015.08
		 * @category Developer
		 * @see client/html/common/decorators/default
		 * @see client/html/checkout/standard/address/decorators/excludes
		 * @see client/html/checkout/standard/address/decorators/global
		 */

		return $this->createSubClient( 'checkout/standard/address/' . $type, $name );
	}


	/**
	 * Processes the input, e.g. store given values.
	 *
	 * A view must be available and this method doesn't generate any output
	 * besides setting view variables.
	 */
	public function process()
	{
		$view = $this->getView();

		try
		{
			parent::process();

			$context = $this->getContext();

			if( ( $param = $view->param( 'ca_extra' ) ) !== null ) {
				$context->getSession()->set( 'client/html/checkout/standard/address/extra', (array) $param );
			}

			// Test if addresses are available
			if( !isset( $view->standardStepActive )
				&& empty( \Aimeos\Controller\Frontend::create( $context, 'basket' )->get()->getAddress( 'payment' ) )
			) {
				$view->standardStepActive = 'address';
				return;
			}
		}
		catch( \Exception $e )
		{
			$this->getView()->standardStepActive = 'address';
			throw $e;
		}
	}


	/**
	 * Returns the list of sub-client names configured for the client.
	 *
	 * @return array List of HTML client names
	 */
	protected function getSubClientNames() : array
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
	public function addData( \Aimeos\MW\View\Iface $view, array &$tags = [], string &$expire = null ) : \Aimeos\MW\View\Iface
	{
		$context = $this->getContext();
		$localeManager = \Aimeos\MShop::create( $context, 'locale' );
		$controller = \Aimeos\Controller\Frontend::create( $context, 'customer' );
		$orderAddressManager = \Aimeos\MShop::create( $context, 'order/base/address' );

		$deliveryAddressItems = [];
		$item = $controller->uses( ['customer/address'] )->get();

		foreach( $item->getAddressItems() as $pos => $addrItem ) {
			$deliveryAddressItems[$pos] = $orderAddressManager->create()->copyFrom( $addrItem );
		}

		$paymentAddressItem = $orderAddressManager->create()
			->setLanguageId( $context->getLocale()->getLanguageId() )
			->copyFrom( $item->getPaymentAddress() );

		$view->addressCustomerItem = $item;
		$view->addressPaymentItem = $paymentAddressItem;
		$view->addressDeliveryItems = $deliveryAddressItems;
		$view->addressLanguages = $localeManager->search( $localeManager->filter( true ) )
			->col( 'locale.languageid', 'locale.languageid' );

		/** common/countries
		 * List of available country codes for frontend and backend
		 *
		 * This configration option is used whenever a list of countries is
		 * shown in the frontend or backend. It's used e.g.
		 * if the customer should select the country he is living in the
		 * checkout process. In case that the list is empty, no country
		 * selection is shown.
		 *
		 * Each list entry must be a two letter ISO country code that is then
		 * translated into its name. The codes have to be upper case
		 * characters like "DE" for Germany or "GB" for Great Britain, e.g.
		 *
		 *  array( 'DE', 'GB', ... )
		 *
		 * @param array List of two letter ISO country codes
		 * @since 2021.10
		 */
		$default = $view->config( 'common/countries', [] );
		/** @deprecated 2022.01 Use common/countries */
		$view->addressCountries = map( $view->config( 'client/html/checkout/standard/address/countries', $default ) )
			->flip()->map( function( $v, $key ) use ( $view ) {
				return $view->translate( 'country', $key );
			} )->asort();

		/** common/states
		 * List of available states for frontend and backend
		 *
		 * This configration option is used whenever a list of states is
		 * shown in the frontend or bakcend. It's used e.g.
		 * if the customer should select the state he is living in the
		 * checkout process. In case that the list is empty, no state
		 * selection is shown.
		 *
		 * A two letter ISO country code must be the key for the list of
		 * states that belong to this country. The list of states must then
		 * contain the state code as key and its name as values, e.g.
		 *
		 *  array(
		 *      'US' => array(
		 *          'CA' => 'California',
		 *          'NY' => 'New York',
		 *          ...
		 *      ),
		 *      ...
		 *  );
		 *
		 * The codes have to be upper case characters like "US" for the
		 * United States or "DE" for Germany. The order of the country and
		 * state codes determine the order of the states in the frontend and
		 * the state codes are later used for per state tax calculation.
		 *
		 * @param array Multi-dimensional list ISO country codes and state codes/names
		 * @since 2020.10
		 */
		$default = $view->config( 'common/states', [] );
		/** @deprecated 2022.01 Use common/states */
		$view->addressStates = $view->config( 'client/html/checkout/standard/address/states', $default );

		$view->addressExtra = $context->getSession()->get( 'client/html/checkout/standard/address/extra', [] );

		return parent::addData( $view, $tags, $expire );
	}
}

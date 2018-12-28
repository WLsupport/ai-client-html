<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2018
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
	/** client/html/checkout/standard/address/standard/subparts
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
	private $subPartPath = 'client/html/checkout/standard/address/standard/subparts';

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
	public function getBody( $uid = '' )
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

		/** client/html/checkout/standard/address/standard/template-body
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
		 * @see client/html/checkout/standard/address/standard/template-header
		 */
		$tplconf = 'client/html/checkout/standard/address/standard/template-body';
		$default = 'checkout/standard/address-body-standard';

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
	public function getSubClient( $type, $name = null )
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

			$basketCntl = \Aimeos\Controller\Frontend\Factory::createController( $context, 'basket' );

			// Test if addresses are available
			$addresses = $basketCntl->get()->getAddresses();
			if( !isset( $view->standardStepActive ) && count( $addresses ) === 0 )
			{
				$view->standardStepActive = 'address';
				return false;
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
		$controller = \Aimeos\Controller\Frontend\Factory::createController( $context, 'customer' );
		$orderAddressManager = \Aimeos\MShop::create( $context, 'order/base/address' );

		try
		{
			$deliveryAddressItems = [];
			$item = $controller->getItem( $context->getUserId(), ['customer/address'] );

			foreach( $item->getAddressItems() as $id => $addrItem ) {
				$deliveryAddressItems[$id] = $orderAddressManager->createItem()->copyFrom( $addrItem );
			}

			$paymentAddressItem = $orderAddressManager->createItem()->copyFrom( $item->getPaymentAddress() );

			$view->addressCustomerItem = $item;
			$view->addressPaymentItem = $paymentAddressItem;
			$view->addressDeliveryItems = $deliveryAddressItems;
		}
		catch( \Exception $e ) {} // customer has no account yet


		$localeManager = \Aimeos\MShop::create( $context, 'locale' );
		$locales = $localeManager->searchItems( $localeManager->createSearch( true ) );

		$languages = [];
		foreach( $locales as $locale ) {
			$languages[$locale->getLanguageId()] = $locale->getLanguageId();
		}

		$view->addressLanguages = $languages;

		/** client/html/checkout/standard/address/countries
		 * List of available countries that that users can select from in the front-end
		 *
		 * This configration option is used whenever a list of countries is
		 * shown in the front-end users can select from. It's used e.g.
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
		 * To display the country selection, you have to add the key for the
		 * country ID (order.base.address.languageid) to the "mandatory" or
		 * "optional" configuration option for billing and delivery addresses.
		 *
		 * Until 2015-02, the configuration option was available as
		 * "client/html/common/address/countries" starting from 2014-03.
		 *
		 * @param array List of two letter ISO country codes
		 * @since 2015.02
		 * @category User
		 * @category Developer
		 * @see client/html/checkout/standard/address/billing/mandatory
		 * @see client/html/checkout/standard/address/billing/optional
		 * @see client/html/checkout/standard/address/delivery/mandatory
		 * @see client/html/checkout/standard/address/delivery/optional
		 */
		$view->addressCountries = $view->config( 'client/html/checkout/standard/address/countries', [] );

		/** client/html/checkout/standard/address/states
		 * List of available states that that users can select from in the front-end
		 *
		 * This configration option is used whenever a list of states is
		 * shown in the front-end users can select from. It's used e.g.
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
		 * To display the country selection, you have to add the key for the
		 * state (order.base.address.state) to the "mandatory" or
		 * "optional" configuration option for billing and delivery addresses.
		 * You also need to add order.base.address.countryid as well because
		 * it is required to display the states that belong to this country.
		 *
		 * Until 2015-02, the configuration option was available as
		 * "client/html/common/address/states" starting from 2014-09.
		 *
		 * @param array Multi-dimensional list ISO country codes and state codes/names
		 * @since 2015.02
		 * @category User
		 * @category Developer
		 * @see client/html/checkout/standard/address/billing/mandatory
		 * @see client/html/checkout/standard/address/billing/optional
		 * @see client/html/checkout/standard/address/delivery/mandatory
		 * @see client/html/checkout/standard/address/delivery/optional
		 */
		$view->addressStates = $view->config( 'client/html/checkout/standard/address/states', [] );

		$view->addressExtra = $context->getSession()->get( 'client/html/checkout/standard/address/extra', [] );

		return parent::addData( $view, $tags, $expire );
	}
}

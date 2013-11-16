<?php namespace Controllers\Admin;

use AdminController;
use Input;
use Lang;
use License;
use DB;
use Redirect;
use Sentry;
use Str;
use Validator;
use View;

class LicensesController extends AdminController {

	/**
	 * Show a list of all the licenses.
	 *
	 * @return View
	 */

	public function getIndex()
	{
		// Grab all the licenses
		$licenses = License::orderBy('created_at', 'DESC')->paginate(10);

		// Show the page
		return View::make('backend/licenses/index', compact('licenses'));
	}


	/**
	 * License create.
	 *
	 * @return View
	 */
	public function getCreate()
	{
		// Show the page
		$license_options = array('0' => 'Top Level') + License::lists('name', 'id');
		return View::make('backend/licenses/edit')->with('license_options',$license_options)->with('license',new License);
	}


	/**
	 * License create form processing.
	 *
	 * @return Redirect
	 */
	public function postCreate()
	{
		// Declare the rules for the form validation
		$rules = array(
			'name'   => 'required|min:3',
			'serial'   => 'required|min:5',
		);

		// Create a new validator instance from our validation rules
		$validator = Validator::make(Input::all(), $rules);

		// If validation fails, we'll exit the operation now.
		if ($validator->fails())
		{
			// Ooops.. something went wrong
			return Redirect::back()->withInput()->withErrors($validator);
		}

		// Create a new license
		$license = new License;

		// Update the license data
		$license->name 				= e(Input::get('name'));
		$license->serial 			= e(Input::get('serial'));
		$license->license_email 	= e(Input::get('license_email'));
		$license->license_name 		= e(Input::get('license_name'));
		$license->notes 			= e(Input::get('notes'));
		$license->order_number 		= e(Input::get('order_number'));
		$license->purchase_date 	= e(Input::get('purchase_date'));
		$license->purchase_cost 	= e(Input::get('purchase_cost'));
		$license->user_id 			= Sentry::getId();

		// Was the license created?
		if($license->save())
		{
			// Redirect to the new license  page
			return Redirect::to("admin/licenses")->with('success', Lang::get('admin/licenses/message.create.success'));
		}

		// Redirect to the license create page
		return Redirect::to('admin/licenses/edit')->with('error', Lang::get('admin/licenses/message.create.error'))->with('license',new License);
	}

	/**
	 * License update.
	 *
	 * @param  int  $licenseId
	 * @return View
	 */
	public function getEdit($licenseId = null)
	{
		// Check if the license exists
		if (is_null($license = License::find($licenseId)))
		{
			// Redirect to the blogs management page
			return Redirect::to('admin/licenses')->with('error', Lang::get('admin/licenses/message.does_not_exist'));
		}

		// Show the page
		//$license_options = array('' => 'Top Level') + License::lists('name', 'id');

		$license_options = array('' => 'Top Level') + DB::table('licenses')->where('id', '!=', $licenseId)->lists('name', 'id');
		return View::make('backend/licenses/edit', compact('license'))->with('license_options',$license_options);
	}


	/**
	 * License update form processing page.
	 *
	 * @param  int  $licenseId
	 * @return Redirect
	 */
	public function postEdit($licenseId = null)
	{
		// Check if the blog post exists
		if (is_null($license = License::find($licenseId)))
		{
			// Redirect to the blogs management page
			return Redirect::to('admin/licenses')->with('error', Lang::get('admin/licenses/message.does_not_exist'));
		}

		// Declare the rules for the form validation
		$rules = array(
			'name'   => 'required|min:3',
			'serial'   => 'required|min:5',
		);

		// Create a new validator instance from our validation rules
		$validator = Validator::make(Input::all(), $rules);

		// If validation fails, we'll exit the operation now.
		if ($validator->fails())
		{
			// Ooops.. something went wrong
			return Redirect::back()->withInput()->withErrors($validator);
		}

		// Update the license data
		$license->name 				= e(Input::get('name'));
		$license->serial 			= e(Input::get('serial'));
		$license->license_email 	= e(Input::get('license_email'));
		$license->license_name 		= e(Input::get('license_name'));
		$license->notes 			= e(Input::get('notes'));
		$license->order_number 		= e(Input::get('order_number'));
		$license->purchase_date 	= e(Input::get('purchase_date'));
		$license->purchase_cost 	= e(Input::get('purchase_cost'));

		// Was the license updated?
		if($license->save())
		{
			// Redirect to the new license page
			return Redirect::to("admin/licenses/$licenseId/edit")->with('success', Lang::get('admin/licenses/message.update.success'));
		}

		// Redirect to the license management page
		return Redirect::to("admin/licenses/$licenseId/edit")->with('error', Lang::get('admin/licenses/message.update.error'));
	}

	/**
	 * Delete the given license.
	 *
	 * @param  int  $licenseId
	 * @return Redirect
	 */
	public function getDelete($licenseId)
	{
		// Check if the blog post exists
		if (is_null($license = License::find($licenseId)))
		{
			// Redirect to the blogs management page
			return Redirect::to('admin/licenses')->with('error', Lang::get('admin/licenses/message.not_found'));
		}

		// Delete the blog post
		$license->delete();

		// Redirect to the blog posts management page
		return Redirect::to('admin/licenses')->with('success', Lang::get('admin/licenses/message.delete.success'));
	}



}
<?php

/**
 * Contact Controller
 * @author James Fairhurst <info@jamesfairhurst.co.uk>
 */
class ContactController extends AppController {

	/**
	 * Main index action
	 */
	public function index() {
		// form posted
		if ($this->request->is('post')) {
			// create
			$this->Contact->create();

			// attempt to save
			if ($this->Contact->save($this->request->data)) {
				$this->Session->setFlash('Your message has been submitted');
				$this->redirect(array('action' => 'index'));

			// form validation failed
			} else {
				// check if file has been uploaded, if so get the file path
				if (!empty($this->Contact->data['Contact']['filepath']) && is_string($this->Contact->data['Contact']['filepath'])) {
					$this->request->data['Contact']['filepath'] = $this->Contact->data['Contact']['filepath'];
				}
			}
		}
	}
}
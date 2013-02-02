<?php
class ContactTest extends CakeTestCase {

	public function setUp() {
		parent::setUp();

		// Load Contact Model
		$this->Contact = ClassRegistry::init('Contact');
	}

	public function tearDown() {
		parent::tearDown();

		// Remove all form submissions
		$this->Contact->query('TRUNCATE TABLE contacts;');

		// Remove Uploaded file
		@unlink(WWW_ROOT.'uploads'.DS.'TestFile.jpg');
	}

	public function testEmptyForm() {
		// Build the data to save
		$data = array(
			'Contact' => array(
				'name' => '',
				'email' => '',
				'message' => '',
			)
		);

		// Attempt to save
		$result = $this->Contact->save($data);

		// Test save failed
		$this->assertFalse($result);
	}

    public function testInvalidEmail() {
		// Build the data to save
		$data = array('Contact' => array(
			'name' => 'James Fairhurst',
			'email' => 'infojamesfairhurst.co.uk',
			'message' => 'Test Message',
		));

		// Attempt to save
		$result = $this->Contact->save($data);

		// Test save failed
		$this->assertFalse($result);
	}

	public function testValidEmail() {
		// Build the data to save
		$data = array('Contact' => array(
			'name' => 'James Fairhurst',
			'email' => 'info@jamesfairhurst.co.uk',
			'message' => 'Test Message',
		));

		// Attempt to save
		$result = $this->Contact->save($data);

		// Test successful insert
		$this->assertArrayHasKey('Contact', $result);

		// Get the count in the DB
		$result = $this->Contact->find('count', array(
			'conditions' => array(
				'Contact.email' => 'info@jamesfairhurst.co.uk',
				'Contact.name' => 'James Fairhurst',
				'Contact.message' => 'Test Message',
			),
		));

		// Test DB entry
		$this->assertEqual($result, 1);
	}

	public function testFormWithEmptyFile() {
		// Build the data to save along with an empty file
		$data = array('Contact' => array(
			'name' => 'James Fairhurst',
			'email' => 'info@jamesfairhurst.co.uk',
			'message' => 'Test Empty File Upload',
			'filename' => array(
				'name' => '',
				'type' => '',
				'tmp_name' => '',
				'error' => 4,
				'size' => 0,
			)
		));

		// Attempt to save
		$result = $this->Contact->save($data);

		// Test successful insert
		$this->assertArrayHasKey('Contact', $result);

		// Get the count in the DB
		$result = $this->Contact->find('count', array(
			'conditions' => array(
				'Contact.email' => 'info@jamesfairhurst.co.uk',
				'Contact.name' => 'James Fairhurst',
				'Contact.message' => 'Test Empty File Upload',
			),
		));

		// Test DB entry
		$this->assertEqual($result, 1);
	}

	public function testFormWithValidFile() {
		// Create a stub for the Contact Model class
		$stub = $this->getMock('Contact', array('is_uploaded_file','move_uploaded_file'));

		// Always return TRUE for the 'is_uploaded_file' function
		$stub->expects($this->any())
			->method('is_uploaded_file')
			->will($this->returnValue(TRUE));
		// Copy the file instead of 'move_uploaded_file' to allow testing
		$stub->expects($this->any())
			->method('move_uploaded_file')
			->will($this->returnCallback('copy'));

		// Build the data to save along with a sample file
		$data = array('Contact' => array(
			'name' => 'James Fairhurst',
			'email' => 'info@jamesfairhurst.co.uk',
			'message' => 'Test File Upload',
			'filename' => array(
				'name' => 'TestFile.jpg',
				'type' => 'image/jpeg',
				'tmp_name' => ROOT.DS.APP_DIR.DS.'Test'.DS.'Case'.DS.'Model'.DS.'TestFile.jpg',
				'error' => 0,
				'size' => 845941,
			)
		));

		// Attempt to save
		$result = $stub->save($data);

		// Test successful insert
		$this->assertArrayHasKey('Contact', $result);

		// Get the count in the DB
		$result = $this->Contact->find('count', array(
			'conditions' => array(
				'Contact.email' => 'info@jamesfairhurst.co.uk',
				'Contact.name' => 'James Fairhurst',
				'Contact.message' => 'Test File Upload',
				'Contact.filename' => 'uploads/TestFile.jpg'
			),
		));

		// Test DB entry
		$this->assertEqual($result, 1);

		// Test uploaded file exists
		$this->assertFileExists(WWW_ROOT.'uploads'.DS.'TestFile.jpg');
	}

	public function testFormWithInvalidFile() {
		// Create a stub for the Contact Model class
		$stub = $this->getMock('Contact', array('is_uploaded_file','move_uploaded_file'));

		// Always return TRUE for the 'is_uploaded_file' function
		$stub->expects($this->any())
			->method('is_uploaded_file')
			->will($this->returnValue(TRUE));
		// Copy the file instead of 'move_uploaded_file' to allow testing
		$stub->expects($this->any())
			->method('move_uploaded_file')
			->will($this->returnCallback('copy'));

		// Build the data to save along with a sample file
		$data = array('Contact' => array(
			'name' => 'James Fairhurst',
			'email' => 'info@jamesfairhurst.co.uk',
			'message' => 'Test File Upload',
			'filename' => array(
				'name' => 'TestFile.txt',
				'type' => 'text/plain',
				'tmp_name' => ROOT.DS.APP_DIR.DS.'Test'.DS.'Case'.DS.'Model'.DS.'TestFile.txt',
				'error' => 0,
				'size' => 19,
			)
		));

		// Attempt to save
		$result = $stub->save($data);

		// Test failure
		$this->assertFalse($result);

		// Test uploaded file does not exists
		$this->assertFileNotExists(WWW_ROOT.'uploads'.DS.'TestFile.txt');
	}
}
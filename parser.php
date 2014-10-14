<?php

class Job
{
	public $title;
	public $id;
	public $url;
	public $tags = [];

	public function __toString()
	{
		return join(",", [$this->id, $this->title, $this->url, join("|", $this->tags)]) . "\n";
	}
}

class Importer
{
	protected $currentJob;
	protected $currentText;

	public function imports($url)
	{
		$parser = xml_parser_create();
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, true);
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, false);
		xml_set_element_handler($parser, [$this, 'tagStart'], [$this, 'tagEnd']);
  		xml_set_character_data_handler($parser, [$this, 'cdata']);
  		xml_parse($parser, file_get_contents($url), true);
  		xml_parser_free($parser);
	}

	protected function tagStart($parser, $tagName, $attributes)
	{
		if ($tagName === "offre") {
			$this->currentJob = new Job();
			$this->currentJob->id = $attributes["monster-id"];
		}
	}

	protected function cdata($parser, $content)
	{
		$this->currentText = $content;
	}

	protected function tagEnd($parser, $tagName)
	{
		if ($tagName === "titre") {
			$this->currentJob->title = $this->currentText;
		}
		else if ($tagName === "page") {
			$this->currentJob->url = $this->currentText;
		}
		else if ($tagName === "motcle") {
			$this->currentJob->tags[] = $this->currentText;
		}
		else if ($tagName === "offre") {
			echo $this->currentJob;
			sleep(2);
		}
	}
}

$parser = new Importer();
$parser->imports(dirname(__FILE__) . "/monster.xml");
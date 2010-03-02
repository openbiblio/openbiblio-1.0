<?php

// Original version by Miguel Carreras based on JS version by
// Andy Chu. Portions by Andy Chu and Steven Roussey.
//
// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at
//
//      http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.

//
// PHP implementation of json-template.
//

 
/*
 * since there are no callbacks nor namespaces in Php 5.2, all classes are prefixed with JsonTemplate
 * and callbacks are faked using the JsonTemplateCallback class
 * when version 5.3 is out it would be better to use namespaces and real callbacks
 */

/*
 * Base class for all exceptions in this module.
 * Thus you can catch JsonTemplateError to catch all exceptions thrown by this module
 */
class JsonTemplateError extends Exception
{
	function __construct($msg,$near=null)
	{
		/*
		This helps people debug their templates.

		If a variable isn't defined, then some context is shown in the traceback.
		TODO: Attach context for other errors.
		 */
		parent::__construct($msg);
		$this->near = $near;
		if($this->near){
			$this->message .= "\n\nNear: ".$this->near;
		}
	}
}

/* 
* Base class for errors that happen during the compilation stage
*/
class JsonTemplateCompilationError extends JsonTemplateError
{

}

/*
 * Base class for errors that happen when expanding the template.
 *
 * This class of errors generally involve the data array or the execution of
 * the formatters.
 */
class JsonTemplateEvaluationError extends JsonTemplateError
{

	function __construct($msg,$original_exception=null)
	{
		parent::__construct($msg);
		$this->original_exception = $original_exception;
	}
}

/*
 * A bad formatter was specified, e.g. {variable|BAD}
 */
class JsonTemplateBadFormatter extends JsonTemplateCompilationError
{

}

/*
 * A bad predicate was specified
 */
class JsonTemplateBadPredicate extends JsonTemplateCompilationError
{

}

/*
 * Raised when formatters are required, and a variable is missing a formatter.
 */
class JsonTemplateMissingFormatter extends JsonTemplateCompilationError
{

}

/*
 * Raised when the Template options are invalid and it can't even be compiled.
 */
class JsonTemplateConfigurationError extends JsonTemplateCompilationError
{

}

/*
 * Syntax error in the template text.
 */
class JsonTemplateTemplateSyntaxError extends JsonTemplateCompilationError
{

}

/*
 * The template contains a variable not defined by the data dictionary.
 */
class JsonTemplateUndefinedVariable extends JsonTemplateCompilationError
{

}

/*
 * represents a callback since PHP has no equivalent
 */
abstract class JsonTemplateCallback
{
	abstract public function call();
}

// calls a function passing all the parameters
class JsonTemplateFunctionCallback extends JsonTemplateCallback
{
	protected $function = '';
	protected $args = array();

	function __construct()
	{
		$args = func_get_args();
		$this->function = array_shift($args);
		$this->args = $args;
	}

	function call()
	{
		$args = func_get_args();
		$args = array_merge($this->args,$args);
		return call_user_func_array($this->function,$args);
	}
}

// stores the first parameter in an array
class JsonTemplateStackCallback extends JsonTemplateCallback
{
	protected $stack = array();

	function call()
	{
		$this->stack[] = func_get_arg(0);
	}

	function get()
	{
		return $this->stack;
	}
}

class JsonTemplateModuleCallback extends JsonTemplateFunctionCallback
{
	function call()
	{
		$module = JsonTemplateModule::pointer();
		$args = func_get_args();
		$args = array_merge($this->args,$args);
		return call_user_func_array(array($module,$this->function),$args);
	}
}

/*
 * Receives method calls from the parser, and constructs a tree of JsonTemplateSection
 * instances.
 */

class JsonTemplateProgramBuilder
{
	/*
	more_formatters: A function which returns a function to apply to the
	value, given a format string.  It can return null, in which case the
	DefaultFormatters class is consulted.
	*/
	function __construct($more_formatters)
	{
		$this->current_block = new JsonTemplateSection();
		$this->stack = array($this->current_block);
		$this->more_formatters = $more_formatters;
	}

    // statement: Append a literal
	function Append($statement)
	{
		$this->current_block->Append($statement);
	}

	function findUserFunction($entire_fn_area, $fn_name, $fn_or_array) {
		if($fn_or_array instanceof JsonTemplateCallback){
			return $fn_or_array->call($fn_name);
		}elseif(is_array($fn_or_array)){
			return $fn_or_array[$fn_name];
		}elseif(function_exists($fn_or_array)){
			return $fn_or_array($fn_name);
		}
		return false;
	}
	
	/*
	 * The user's formatters are consulted first, then the default formatters.
	 * 
	 * 
	 * more_formatters can be a:
	 *     - JsonTemplateCallback
	 *     - array of functions
	 *     - function
	 *     
	 * more_formatters is referenced first and when those formatters are 
	 * actually called, they get a single parameter (data).
	 * 
	 */ 
	protected function GetFormatter($format_str)
	{
		preg_match('/^\s*([A-Za-z0-9_-]+)(.*?)?$/',$format_str,$matches);
		$formatter_fn=$matches[1];
		$arg_string=$matches[2];
		
		// more_formatters first		
		$formatter=$this->findUserFunction($format_str, $formatter_fn, $this->more_formatters);
		if ($formatter) {
			return array($formatter,$arg_string);
		}
		$formatter = JsonTemplateModule::pointer()->formatters[$formatter_fn];
		if($formatter) {
			return array($formatter,$arg_string);
		}
		
		throw new JsonTemplateBadFormatter(sprintf('%s is not a valid formatter!!!', $format_str));
	}

	protected function GetPredicate($predicate_str)
	{
		preg_match('/^\s*([A-Za-z0-9_-]+\??)(.*?)?$/',$predicate_str,$matches);
		$predicate_fn=$matches[1];
		$arg_string=$matches[2];
		
		// more_predicates first
		$predicate=$this->findUserFunction($predicate_str, $predicate_fn, $this->more_predicates);
		if ($predicate) {
			return array($predicate,$arg_string);
		}
		$predicate = JsonTemplateModule::pointer()->predicates[$predicate_fn];
		if($predicate) {
			return array($predicate,$arg_string);
		}
		
		throw new JsonTemplateBadPredicate(sprintf('%s is not a valid predicate', $predicate_str));
	}
	
	function AppendSubstitution($name, $formatters)
	{
		foreach($formatters as $k=>$f){
			$formatters[$k] = $this->GetFormatter($f);
		}
		$this->current_block->Append(new JsonTemplateModuleCallback('DoSubstitute', $name, $formatters));

	}

	// For sections or repeated sections
	function NewSection($repeated, $section_name, $predicate_target, $predicate)
	{
		$predicate_fn=$this->GetPredicate($predicate);
		$new_block = new JsonTemplateSection($section_name, $predicate_target, $predicate_fn);
		$this->current_block->Append(new JsonTemplateModuleCallback('DoSection', $repeated, $new_block));
		$this->stack[] = $new_block;
		$this->current_block = $new_block;
	}

	/*
	 * TODO: throw errors if the clause isn't appropriate for the current block
	 * isn't a 'repeated section' (e.g. alternates with in a non-repeated
	 * section)
	 */
	function NewClause($name)
	{
		$this->current_block->NewClause($name);
	}

	function EndSection()
	{
		array_pop($this->stack);
		$this->current_block = end($this->stack);
	}

	function Root()
	{
		return $this->current_block;
	}
}

// Represents a (repeated) section.
class JsonTemplateSection
{
	/*
	 * Args:
	 * section_name: name given as an argument to the section
	 */
	function __construct($section_name=null, $predicate_target=null, $predicate=null)
	{
		$this->section_name = $section_name;
		$this->predicate_target = $predicate_target;
		$this->predicate = $predicate;
		$this->current_clause = array();
		$this->statements = array('default'=>&$this->current_clause);
	}

	function __toString()
	{
		try{
			return sprintf('<Block %s>', $this->section_name);
		}catch(Exception $e){
			return $e->getMessage();
		}
	}

	function Statements($clause='default')
	{
		return $this->statements[$clause];
	}

	function NewClause($clause_name)
	{
		$new_clause = array();
		$this->statements[$clause_name] = &$new_clause;
		$this->current_clause = &$new_clause;
	}

	// Append a statement to this block.
	function Append($statement)
	{
		array_push($this->current_clause, $statement);
	}
}


/*
 * Allows scoped lookup of variables.
 * If the variable isn't in the current context, then we search up the stack.
 */
class JsonTemplateScopedContext implements Iterator
{
	protected $positions = array();

	function __construct($context,$undefined_str=null)
	{
		$this->undefined_str=$undefined_str;
		$this->stack = array($context);
		$this->name_stack = array('@');
		$this->positions = array(0);
	}

	function __toString()
	{
		return sprintf("<Context %s>",implode(" ",$this->name_stack));
	}

	function PushSection($name)
	{
		$end = end($this->stack);
		$new_context=null;
		if(is_array($end)){
			if(isset($end[$name])){
				$new_context = $end[$name];
			}
		}elseif(is_object($end)){
			// since json_decode returns StdClass
			// check if scope is an object
			if(property_exists($end,$name)){
				$new_context = $end->$name;
			} else if (method_exists($end,$getter="get$name")){
				$new_context = $end->$getter();
			} else if (method_exists($end,'__get')){
				try {
					$new_context = $end->$name;
				} catch (exception $e){}
			}
		}
		$this->name_stack[] = $name;
		$this->stack[] = $new_context;
		$this->positions[] = 0;
		return $new_context;
	}

	function Pop()
	{
		array_pop($this->positions);
		array_pop($this->name_stack);
		return array_pop($this->stack);
	}

	function CursorValue()
	{
		return end($this->stack);
	}

	// Iterator functions
	// Assumes that the top of the stack is a list.
	// NOTE: Iteration alters scope
	function rewind() {
		$this->positions[] = 0;
		$this->stack[] = array();
	}

	function current() {
		return end($this->stack);
	}

	function key() {
		return end($this->positions);
	}

	function next() {
		++$this->positions[count($this->positions)-1];
	}

	function valid() {
		$len = count($this->stack);
		$pos = end($this->positions);
		$items = $this->stack[$len-2];
		if(is_array($items) && count($items)>$pos){
			$this->stack[$len-1] = $items[$pos];
			return true;
		}else{ //end of iteration -- reverses rewind call at begining of iteration (assumes no halting or errors)
			array_pop($this->stack);
			array_pop($this->positions);
			return false;
		}
	}

	function Undefined($name) {
		if ($this->undefined_str === null) {
			throw new JsonTemplateUndefinedVariable(sprintf('%s is not defined',$name));
		} else {
			return $this->undefined_str;
		}
	}

	// Get the value associated with a name in the current context.	The current
	// context could be an associative array or a StdClass object
 	function Lookup($name) {
		if ($name == '@') {
			return end($this->stack);
		}
		$parts = explode('.',$name);
		$value = $this->_LookUpStack($parts[0]);
		$count=count($parts);
		if ($count > 1) {
			for ($i = 1; $i < $count; $i++) {
				$namepart=$parts[$i];
				if(is_array($value)){
					if(!isset($value[$namepart])){
						return $this->Undefined($name);
					}else{
						$value= $value[$namepart];
					}
				}elseif(is_object($value)){
					if(property_exists($value,$namepart)){
						$value= $value->$namepart;
					}else if(method_exists($value,$getter="get$namepart")){
						$value= $value->$getter();
					} else if (method_exists($value,'__get')){
						try {
							$value= $value->$namepart;
						} catch (exception $e){
							return $this->Undefined($name);
						}
					} else {
						return $this->Undefined($name);
					}
				} else {
					return $this->Undefined($name);
				}
			}
		}
		return $value;
	}
	
	function _LookUpStack($name)
	{
		$i = count($this->stack)-1;
		while(true){
			$context = $this->stack[$i];
			if ($name=='@index'){
				$key=$this->key();
				if($key==-1){
					$i--;
				} else {
					return $key+1;
				}
			} else {
				if(is_array($context)){
					if(!isset($context[$name])){
						$i -= 1;
					}else{
						return $context[$name];
					}
				}elseif(is_object($context)){
					if(property_exists($context,$name)){
						return $context->$name;
					}else if(method_exists($context,$getter="get$name")){
						return $context->$getter();
					} else if (method_exists($context,'__get')){
						try {
							return $context->$name;
						} catch (exception $e){
							$i -= 1;
						}
					}else{
						$i -= 1;
					}
				}else{
					$i -= 1;
				}
			}
			if($i<= -1){
				return $this->Undefined($name);
			}
		}
	}
}


# See http://google-ctemplate.googlecode.com/svn/trunk/doc/howto.html for more
# escape types.
#
# Also, we might want to take a look at Django filters.
abstract class JsonTemplateFormatter
{
	abstract public function format($obj);
}

class HtmlJsonTemplateFormatter extends JsonTemplateFormatter
{
	function format($str)
	{
		return htmlspecialchars($str,ENT_NOQUOTES);
	}
}

class HtmlAttributeValueJsonTemplateFormatter extends JsonTemplateFormatter
{
	function format($str)
	{
		return htmlspecialchars($str);
	}

}

class RawJsonTemplateFormatter extends JsonTemplateFormatter
{
	function format($str)
	{
		return $str;
	}
}

class StringJsonTemplateFormatter extends JsonTemplateFormatter
{
	function format($str)
	{
		if ($str===null)
			return 'null';
		return (string)$str;
	}
}

class SizeJsonTemplateFormatter extends JsonTemplateFormatter
{
	# Used for the length of an array or a string
	function format($obj)
	{
		if(is_string($obj)){
			return strlen($obj);
		}else{
			return count($obj);
		}
	}
}

class UrlParamsJsonTemplateFormatter extends JsonTemplateFormatter
{
    	# The argument is an associative array, and we get a a=1&b=2 string back.
	function format($params)
	{
		if(is_array($parmas)){
			foreach($params as $k=>$v){
				$parmas[$k] = urlencode($k)."=".urlencode($v);
			}
			return implode("&",$params);
		}else{
			return urlencode($params);
		}
	}
}

class UrlParamValueJsonTemplateFormatter extends JsonTemplateFormatter
{
    	# The argument is a string 'Search query?' -> 'Search+query%3F'
	function format($param)
	{
		return urlencode($param);
	}

}

function PluralizeJsonTemplateFormatter($val,$unused_context,$args)
{
	$args=trim($args);
	$args = $args ? $args : 's';
	if ($args[0]=='/') {
		$args=substr($args,1);
		$args=explode('/',$args);
	}
	else {
		$args=explode(' ',$args);
	}
	if (count($args)==2)
		return $val > 1 ? $args[1] : $args[0];
	return $val > 1 ? $args[0] : '';
}


function AbsoluteUrlJsonTemplateFormatter($relative_url,$context)
{
	return $context->Lookup('base-url') . '/' . $relative_url;
}

function DebugJsonTemplatePredicate($data,$context,$arg)
{
	$debug=false;
	try {
		$debug = !!$context->Lookup('debug');
	} catch (exception $e) {}
	return $debug;
}

class JsonTemplateModule
{

	public $section_re = '/^(?:(repeated)\s+)?(section)\s+(@|[A-Za-z0-9_-]+)(?:\b(.*))?$/';
	public $if_re = '/^if\s+(@|[A-Za-z0-9_-]+)(\?)?(?:\b(.*?))?\s*$/';
	public $option_re = '/^([a-zA-Z\-]+):\s*(.*)/';
	public $option_names = array('meta','format-char','default-formatter');
	public $token_re_cache = array();

	public $formatters = array(
		'html'				=> 'HtmlJsonTemplateFormatter',
		'html-attr-value'	=> 'HtmlAttributeValueJsonTemplateFormatter',
		'htmltag'			=> 'HtmlAttributeValueJsonTemplateFormatter',
		'raw'				=> 'RawJsonTemplateFormatter',
		'size'				=> 'SizeJsonTemplateFormatter',
		'url-params'		=> 'UrlParamsJsonTemplateFormatter',
		'url-param-value'	=> 'UrlParamValueJsonTemplateFormatter',
		'pluralize'			=> 'PluralizeJsonTemplateFormatter',
		'AbsUrl'			=> 'AbsoluteUrlJsonTemplateFormatter',
		'str'				=> 'StringJsonTemplateFormatter',
		'default_formatter'	=> 'StringJsonTemplateFormatter',
	);
	public $predicates = array(
		'true'				=> 'IsTrueJsonTemplatePredicate',
		'Debug?'			=> 'DebugJsonTemplatePredicate'
	);

	static function &pointer()
	{
		static $singleton = null;
		if(!$singleton){       
			$singleton = new JsonTemplateModule();
		}
		return $singleton;
	}

	/*
	 * Split and validate metacharacters.
	 *
	 * Example: '{}' -> ('{', '}')
	 *
	 * This is public so the syntax highlighter and other tools can use it.
	 */
	function SplitMeta($meta)
	{
		$n = strlen($meta);
		if($n % 2 == 1){
			throw new JsonTemplateConfigurationError(sprintf('%s has an odd number of metacharacters', $meta));
		}
		return array(substr($meta,0,$n/2),substr($meta,$n/2));
	}

	/* Return a regular expression for tokenization.
	 * Args:
	 *   meta_left, meta_right: e.g. '{' and '}'
	 *
	 * - The regular expressions are memoized.
	 * - This function is public so the syntax highlighter can use it.
	 */
	function MakeTokenRegex($meta_left, $meta_right)
	{
		$key = $meta_left.$meta_right;
		if(!in_array($key,array_keys($this->token_re_cache))){
			$this->token_re_cache[$key] = '/('.quotemeta($meta_left).'.+?'.quotemeta($meta_right).'\n?)/';
		}
		return $this->token_re_cache[$key];
	}

	/*
	  Compile the template string, calling methods on the 'program builder'.

	  Args:
	    template_str: The template string.  It should not have any compilation
		options in the header -- those are parsed by FromString/FromFile
	    options: array of compilation options, possible keys are:
		    meta: The metacharacters to use
		    more_formatters: A function which maps format strings to
			*other functions*.  The resulting functions should take a data
			array value (a JSON atom, or an array itself), and return a
			string to be shown on the page.  These are often used for HTML escaping,
			etc.  There is a default set of formatters available if more_formatters
			is not passed.
		    default_formatter: The formatter to use for substitutions that are missing a
			formatter.  The 'str' formatter the "default default" -- it just tries
			to convert the context value to a string in some unspecified manner.
	    builder: Something with the interface of JsonTemplateProgramBuilder

	  Returns:
	    The compiled program (obtained from the builder)

	  Throws:
	    The various subclasses of JsonTemplateCompilationError.  For example, if
	    default_formatter=null, and a variable is missing a formatter, then
	    MissingFormatter is raised.

	  This function is public so it can be used by other tools, e.g. a syntax
	  checking tool run before submitting a template to source control.
	*/
	function CompileTemplate($template_str, $options=array(), $builder=null)
	{
		$options=JsonTemplate::processDefaultOptions($options);
		
		if(!$builder){
			$builder = new JsonTemplateProgramBuilder($options['more_formatters']);
		}
		list($meta_left,$meta_right) = $this->SplitMeta($options['meta']);

		# : is meant to look like Python 3000 formatting {foo:.3f}.  According to
		# PEP 3101, that's also what .NET uses.
		# | is more readable, but, more importantly, reminiscent of pipes, which is
		# useful for multiple formatters, e.g. {name|js-string|html}
		if(!in_array($options['format_char'],array(':','|'))){
			throw new JsonTemplateConfigurationError(sprintf('Only format characters : and | are accepted (got %s)',$options['format_char']));
		}

		# Need () for preg_split
		$token_re = $this->MakeTokenRegex($meta_left, $meta_right);
		$tokens = preg_split($token_re, $template_str, -1, PREG_SPLIT_DELIM_CAPTURE);

		# If we go to -1, then we got too many {end}.  If end at 1, then we're missing
		# an {end}.
		$balance_counter = 0;
		foreach($tokens as $i=>$token){
			if(($i % 2) == 0){
				if($token){
					$builder->Append($token);
				}
			}else{
				$had_newline = false;
				if(substr($token,-1)=="\n"){
				 	$token = substr($token,0,-1);
					$had_newline = true;
				}

				assert('substr($token,0,strlen($meta_left)) == $meta_left;');
				assert('substr($token,-1*strlen($meta_right)) == $meta_right;');

				$token = substr($token,strlen($meta_left),-1*strlen($meta_right));

				// if it is a comment
				if(substr($token,0,1)=="#"){
					continue;
				}

				$literal='';
				// if it's a keyword directive
				if(substr($token,0,1)=='.'){
					$token = substr($token,1);
					switch($token){
						case 'meta-left':
							$literal = $meta_left;
							break;
						case 'meta-right':
							$literal = $meta_right;
							break;
						case 'space':
							$literal = ' ';
							break;
						case 'tab':
							$literal = "\t";
							break;
						case 'newline':
							$literal = "\n";
							break;
					}
				}
				if($literal){
					$builder->Append($literal);
					continue;
				}

				if(preg_match($this->section_re,$token,$section_match)){
					$repeated = $section_match[1];
					$section_name = $section_match[3];
					$builder->NewSection($repeated,$section_name,$section_name,'true');
					$balance_counter += 1;
					continue;
				}

				if(preg_match($this->if_re,$token,$section_match)){
					if ($section_match[2]){
						$predicate_target = '@';
						$predicate = $section_match[1].$section_match[2];
					} else {
						$predicate_target = $section_match[1];
						$predicate = $section_match[2];
					}
					if (trim($predicate)=='') {
						$predicate = 'true';
					}
					$builder->NewSection(false,'@',$predicate_target?$predicate_target:'@',$predicate);
					$balance_counter += 1;
					continue;
				}

				if($token=='or'){
					$builder->NewClause($token);
					continue;
				}

				if($token=='alternates with'){
					$builder->NewClause($token);
					continue;
				}

				if($token == 'end'){
					$balance_counter -= 1;
					if($balance_counter < 0){
						# TODO: Show some context for errors
						throw new JsonTemplateTemplateSyntaxError(sprintf(
							'Got too many %s.end%s statements.  You may have mistyped an '.
							"earlier %s.section%s or %s.repeated section%s directive.",
							$meta_left, $meta_right,$meta_left, $meta_right,$meta_left, $meta_right));
					}
					$builder->EndSection();
					if($had_newline){
						$builder->Append("\n");
					}
					continue;
				}

				# Now we know the directive is a substitution.
				$parts = explode($options['format_char'],$token);
				if(count($parts) == 1){
					if(!$options['default_formatter']){
						throw new JsonTemplateMissingFormatter('This template requires explicit formatters.');
						# If no formatter is specified, the default is the 'str' formatter,
						# which the user can define however they desire.
					}
					$name = $token;
					$formatters = array($options['default_formatter']);
				}else{
					$name = array_shift($parts);
					$formatters = $parts;
				}

				$builder->AppendSubstitution($name,$formatters);
				if($had_newline){
					$builder->Append("\n");
				}
			}
		}

		if($balance_counter != 0){
			throw new JsonTemplateTemplateSyntaxError(sprintf('Got too few %send%s statements', $meta_left, $meta_right));
		}
		return $builder->Root();
	}


  	// Like FromString, but takes a file.
	static function FromFile($f, $constructor='JsonTemplate')
	{
		if(is_string($f)){
			$string = file_get_contents($f);
		}else{
			while(!feof($f)){
				$string .= fgets($f,1024)."\n";
			}
		}
		return $this->FromString($string,$constructor);
	}

	/*
	Parse a template from a string, using a simple file format.

	This is useful when you want to include template options in a data file,
	rather than in the source code.

	The format is similar to HTTP or E-mail headers.  The first lines of the file
	can specify template options, such as the metacharacters to use.  One blank
	line must separate the options from the template body.

	Example:

	default-formatter: none
	meta: {{}}
	format-char: :
	<blank line required>
	Template goes here: {{variable:html}}
	*/

	function FromString($string, $constructor='JsonTemplate')
	{
		$options = array();
		$lines = explode("\n",$string);
		foreach($lines as $k=>$line){
			if(preg_match($this->option_re,$line,$match)){
			# Accept something like 'Default-Formatter: raw'.  This syntax is like
			# HTTP/E-mail headers.
				$name = strtolower($match[1]);
				$value = trim($match[2]);
				if(in_array($name,$this->option_names)){
					$name = str_replace('-','_',$name);
					if($name == 'default_formatter' && strtolower($value) == 'none'){
						$value = null;
					}
					$options[$name] = $value;
				}else{
					break;
				}
			}else{
				break;
			}
		}

		if($options){
			if(trim($line)){
				throw new JsonTemplateCompilationError(sprintf(
					'Must be one blank line between template options and body (got %s)',$line));
			}
			$body = implode("\n",array_slice($lines,$k+1));
		}else{
			# There were no options, so no blank line is necessary.
			$body = $string;
		}
		return new $constructor($body,$options);
	}

	// Test predicate condition
	function DoPredicate($block,$context)
	{
		try {
			$predicate_target_value = $context->Lookup($block->predicate_target);
		}catch(JsonTemplateUndefinedVariable $e){
			$predicate_target_value=null;
		}
		$predicate=$block->predicate;
		$do_section=$predicate[0]($predicate_target_value,$context,$predicate[1]);
		return $do_section;
	}
	
	function DoSection($repeated, $block, $context, $callback)
	{
		$do_section=$this->DoPredicate($block,$context);
		
		if($block->section_name == '@'){
			# If the name is @, we stay in the enclosing context
			$items = $context->Lookup('@');
			$pushed = false;
		}else{
			$items = $context->PushSection($block->section_name);
			$pushed = true;
		}

		if($do_section){
			$statements = $block->Statements();
			if ($repeated){
				if(!is_array($items)){
					throw new JsonTemplateEvaluationError(sprintf('Expected a list; got %s', gettype($items)));
				}
				$last_index = count($items) - 1;
				$alt_statements = $block->Statements('alternates with');
				# NOTE: Iteration mutates the context!
				foreach($context as $i=>$data){
					# Execute the statements in the block for every item in the list.  Execute
					# the alternate block on every iteration except the last.
					# Each item could be an atom (string, integer, etc.) or a dictionary.
					$this->Execute($statements, $context, $callback);
					if($i != $last_index){
						$this->Execute($alt_statements, $context, $callback);
					}
				}
			} else {
				$this->Execute($statements, $context, $callback);
			}
		}else{
			$this->Execute($block->Statements('or'), $context, $callback);
		}

		if($pushed){
			$context->Pop();
		}

	}

	// Variable substitution, e.g. {foo}
	function DoSubstitute($name, $formatters, $context, $callback=null)
	{
		if(!($context instanceof JsonTemplateScopedContext)){
			throw new JsonTemplateEvaluationError(sprintf('Error not valid context %s',$context));
		}
		# So we can have {.section is_new}new since {@}{.end}.  Hopefully this idiom
		# is OK.

		try{
			$value = $context->Lookup($name);
		}catch(JsonTemplateUndefinedVariable $e){
			throw $e;
		}catch(Exception $e){
			throw new JsonTemplateEvaluationError(sprintf(
				'Error evaluating %s in context %s: %s', $name, $context, $e->getMessage()
			));
		}
		
		
		foreach($formatters as $f){
			try{
				$fn=$f;
				$args=null;
				if (is_array($f)){
					$fn=$f[0];
					$args=$f[1];
				}
				if (function_exists($fn)){
					$value= $fn($value,$context,$args);
				} else {
					$formatter = new $fn();
					$value = $formatter->format($value,$context,$args);
				}
			}catch(Exception $e){
				if (is_array($f))
					$f=$f[0];
				throw new JsonTemplateEvaluationError(sprintf(
					'Formatting value %s with formatter %s raised exception: %s',
					 $value, $f, $e), $e);
			}
		}
		if($callback instanceof JsonTemplateCallback){
			return $callback->call($value);
		}elseif(is_string($callback)){
			return $callback($value);
		}else{
			return $value;
		}
	}

	/*
	 * Execute a bunch of template statements in a ScopedContext.
  	 * Args:
         * callback: Strings are "written" to this callback function.
	 *
  	 * This is called in a mutually recursive fashion.
	 */
	function Execute($statements, $context, $callback)
	{
		if(!is_array($statements)){
			$statements = array($statements);
		}
		foreach($statements as $i=>$statement){
			if(is_string($statement)){
				if($callback instanceof JsonTemplateCallback){
					$callback->call($statement);
				}elseif(is_string($callback)){
					$callback($statement);
				}
			}else{
				try{
					if($statement instanceof JsonTemplateCallback){
						$statement->call($context, $callback);
					}
				}catch(JsonTemplateUndefinedVariable $e){
					# Show context for statements
					$start = max(0,$i-3);
					$end = $i+3;
					$e->near = array_slice($statements,$start,$end);
					throw $e;
				}
			}
		}
	}

	/*
	Free function to expands a template string with a data dictionary.

	This is useful for cases where you don't care about saving the result of
	compilation (similar to re.match('.*', s) vs DOT_STAR.match(s))
	*/
	function expand($template_str, $data, $options=array())
	{
		$t = new JsonTemplate($template_str, $options);
		return $t->expand($data);
	}

}


/*
Represents a compiled template.

Like many template systems, the template string is compiled into a program,
and then it can be expanded any number of times.  For example, in a web app,
you can compile the templates once at server startup, and use the expand()
method at request handling time.  expand() uses the compiled representation.

There are various options for controlling parsing -- see CompileTemplate.
Don't go crazy with metacharacters.  {}, [], {{}} or <> should cover nearly
any circumstance, e.g. generating HTML, CSS XML, JavaScript, C programs, text
files, etc.
*/

class JsonTemplate
{
	protected $program;
	
	/*
	 * This function add the passed options to the default options.
	 * If the passed options are in object form, they are converted 
	 * to associative array form first, so the class can always 
	 * access options in the array notation.
	 */
	static function processDefaultOptions($options){
		if(is_string($options)){
			$options = json_decode($options);
		}
		$default_options = array(
			'undefined_str'		=> null,
			'meta'				=> '{}',
			'format_char' 		=> '|',
			'more_formatters'	=> null,
			'default_formatter'	=> 'str',
		);
		if(is_object($options)){
			$options = array_merge($default_options,get_object_vars($options));
		}else if(is_array($options)){
			$options = array_merge($default_options,$options);
		}else{
			$options = $default_options;
		}
		return $options;
	}
	/*
	Args:
	template_str: The template string.

	It also accepts all the compile options that CompileTemplate does.
	*/
	function __construct($template_str, $options=array(), $builder=null)
	{
		$options=self::processDefaultOptions($options);

		if ($options['formatters'])
			$this->formatters=array_merge($this->formatters,$options['formatters']);
		if ($options['predicates'])
			$this->predicates=array_merge($this->predicates,$options['predicates']);

		$this->compile_options = $options;
		$this->template_str = $template_str;
	    $this->program = JsonTemplateModule::pointer()->CompileTemplate($template_str, $options, $builder);
	}

	#
	# Public API
	#

	/*
	Low level method to expands the template piece by piece.

	Args:
	data: The JSON data dictionary.
	callback: A callback which should be called with each expanded token.

	Example: You can pass 'f.write' as the callback to write directly to a file
	handle.
	 */
	function render($data, $callback=null)
	{
		if(is_string($data)){
			$data = json_decode($data);
		}
		$JM=JsonTemplateModule::pointer();
		return $JM->Execute(
			$this->program->Statements(), 
			new JsonTemplateScopedContext($data,$this->compile_options['undefined_str']), 
			$callback
		);
	}

	/*
	Expands the template with the given data dictionary, returning a string.

	This is a small wrapper around render(), and is the most convenient
	interface.

	Args:
	data_dict: The JSON data dictionary.

	Returns:
	The return value could be a str() or unicode() instance, depending on the
	the type of the template string passed in, and what the types the strings
	in the dictionary are.
	 */
	function expand($data)
	{
		return implode('',$this->tokenstream($data));
	}

	/*
	returns a list of tokens resulting from expansion.
	*/
	function tokenstream($data)
	{
		$c = new JsonTemplateStackCallback();
		$tokens = $this->render($data,$c);
		return $c->get();
	}
}


function IsValueJsonTemplatePredicateEqual($val)
{
	return !!$val;
}
function IsValueJsonTemplatePredicateNot($val)
{
	return !$val;
}
function IsValueJsonTemplatePredicate($str,$context,$args)
{
	preg_match('/^\s*(not\s+)?"?(.*?)"?\s*$/',$args,$matches);
	$val=$matches[2];
	if($matches[1]=='not')
		$check_fn='IsValueJsonTemplatePredicateNot';
	else
		$check_fn='IsValueJsonTemplatePredicateEqual';
	if ($str===true || $str==="true")
		return $check_fn($val===true || $val==='true');
	if ($str===false || $str==="false")
		return $check_fn($val===false || $val==='false');
	return $check_fn($str==$val);
}

function IsTrueJsonTemplatePredicate($val)
{
	if ($val===null || $val===false || $val==='false')
		return false;
	if (is_array($val)){
		return count($val)>0;
	}
	return !!$val;
}

function IsFalseJsonTemplatePredicate($val)
{
	if ($val===null || $val===false || $val==='false')
		return true;
	if (is_array($val))
		return count($val)==0;
	return !$val;
}
?>
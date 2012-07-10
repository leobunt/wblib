<?php

/**

  Directory helper class

  Copyright (C) 2009, Bianka Martinovic
  Contact me: blackbird(at)webbird.de, http://www.webbird.de/

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 3 of the License, or (at
  your option) any later version.

  This program is distributed in the hope that it will be useful, but
  WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
  General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, see <http://www.gnu.org/licenses/>.

**/

class wbDirectory extends wbBase {

	    protected $recurse = true;
	    protected $prefix  = NULL;
	    protected $suffix_filter = array();
	    protected $skip_dirs     = array();

	    /**
	     * shortcut method for scanDirectory( $dir, $remove_prefix, true, true )
	     **/
		public function getFiles( $dir, $remove_prefix = NULL ) {
		    return $this->scanDirectory( $dir, true, true, $remove_prefix );
		}   // end function getFiles()
		
		/**
         * shortcut method for scanDirectory( $dir, $remove_prefix, true, true, array($suffix) )
         **/
        public function getFilesBySuffix( $dir, $suffix = 'php', $remove_prefix = NULL ) {
            return $this->scanDirectory( $dir, true, true, $remove_prefix, array($suffix) );
        }   // end function getFilesBySuffix()

		/**
	     * shortcut method for scanDirectory( $dir, $remove_prefix, false, false )
	     **/
		public function getDirectories( $dir, $remove_prefix = NULL ) {
		    return $this->scanDirectory( $dir, false, false, $remove_prefix );
		}   // end function getFiles()

	    /**
	     * shortcut method for scanDirectory( $dir, $remove_prefix, true, true, array('php') )
	     **/
		public function getPHPFiles( $dir, $remove_prefix = NULL ) {
		    return $this->scanDirectory( $dir, true, true, $remove_prefix, array('php') );
		}   // end function getPHPFiles()

		/**
	     * shortcut method for scanDirectory( $dir, $remove_prefix, true, true, array('lte','htt','tpl') )
	     **/
		public function getTemplateFiles( $dir, $remove_prefix = NULL ) {
		    return $this->scanDirectory( $dir, true, true, $remove_prefix, array('lte','htt','tpl') );
		}   // end function getTemplateFiles()

		/**
		 * fixes a path by removing //, /../ and other things
		 *
		 * @access public
		 * @param  string  $path - path to fix
		 * @return string
		 **/
		public function sanitizePath( $path ) {
		    // remove / at end of string; this will make sanitizePath fail otherwise!
		    $path       = preg_replace( '~/$~', '', $path );
		    // make all slashes forward
			$path       = str_replace( '\\', '/', $path );
	        // bla/./bloo ==> bla/bloo
	        $path       = preg_replace('~/\./~', '/', $path);
	        // resolve /../
	        // loop through all the parts, popping whenever there's a .., pushing otherwise.
	        $parts      = array();
	        foreach ( explode('/', preg_replace('~/+~', '/', $path)) as $part ) {
	            if ($part === ".." || $part == '') {
	                array_pop($parts);
	            }
	            elseif ($part!="") {
	                $parts[] = $part;
	            }
	        }

	        $new_path = implode("/", $parts);
	        // windows
	        if ( ! preg_match( '/^[a-z]\:/i', $new_path ) ) {
				$new_path = '/' . $new_path;
			}

	        return $new_path;

		}   // end function sanitizePath()

		/**
		 * scans a directory
		 *
		 * @access public
		 * @param  string  $dir - directory to scan
		 * @param  boolean $with_files    - list files too (true) or not (false); default: false
		 * @param  boolean $files_only    - list files only (true) or not (false); default: false
		 * @param  string  $remove_prefix - will be removed from the path names; default: NULL
		 * @param  array   $suffixes      - list of suffixes; only if $with_files = true
		 * @param  array   $skip_dirs     - list of directories to skip
		 *
		 * Examples:
		 *   - get a list of all subdirectories (no files)
		 *     $dirs = $obj->scanDirectory( <DIR> );
		 *
		 *   - get a list of files only
		 *     $files = $obj->scanDirectory( <DIR>, NULL, true, true );
		 *
		 *   - get a list of files AND directories
		 *     $list = $obj->scanDirectory( <DIR>, NULL, true );
		 *
		 *   - remove a path prefix
		 *     $list = $obj->scanDirectory( '/my/abs/path/to', '/my/abs/path' );
		 *     => result is /to/subdir1, /to/subdir2, ...
		 *
		 **/
		function scanDirectory( $dir, $with_files = false, $files_only = false, $remove_prefix = NULL, $suffixes = array(), $skip_dirs = array() ) {

			$dirs = array();

			// make sure $suffixes is an array
            if ( $suffixes && is_scalar($suffixes) ) {
                $suffixes = array( $suffixes );
			}
			if ( ! count($suffixes) && count( $this->suffix_filter ) ) {
			    $suffixes = $this->suffix_filter;
			}
			// make sure $skip_dirs is an array(
			if ( $skip_dirs && is_scalar($skip_dirs) ) {
			    $skip_dirs = array( $skip_dirs );
			}
			if ( ! count($skip_dirs) && count( $this->skip_dirs ) ) {
			    $skip_dirs = $this->skip_dirs;
			}
			if ( ! $remove_prefix && $this->prefix ) {
			    $remove_prefix = $this->prefix;
			}

			if (false !== ($dh = @opendir( $dir ))) {
                while( false !== ($file = @readdir($dh))) {
                    if ( ! preg_match( '#^\.#', $file ) ) {
						if ( count($skip_dirs) && in_array( pathinfo($dir.'/'.$file,PATHINFO_DIRNAME), $skip_dirs) ) {
						    continue;
						}
                        if ( is_dir( $dir.'/'.$file ) ) {
                            if ( ! $files_only ) {
                                $dirs[]  = str_ireplace( $remove_prefix, '', $this->sanitizePath($dir.'/'.$file) );
                            }
                            if ( $this->recurse ) {
                            	// recurse
                            	$subdirs = $this->scanDirectory( $dir.'/'.$file, $with_files, $files_only, $remove_prefix, $suffixes, $skip_dirs );
                            	$dirs    = array_merge( $dirs, $subdirs );
							}
                        }
                        elseif ( $with_files ) {
                            if ( ! count($suffixes) || in_array( pathinfo($file,PATHINFO_EXTENSION), $suffixes ) ) {
                            	$dirs[]  = str_ireplace( $remove_prefix, '', $this->sanitizePath( $dir.'/'.$file ) );
							}
                        }
                    }
                }
            }
            return $dirs;
        }   // end function scanDirectory()

		/**
		 *
		 **/
		public function setPrefix( $prefix ) {
		    if ( is_scalar($prefix) ) {
		        $this->prefix = $prefix;
		        return;
			}
			// reset
			if ( is_null($prefix) ) {
			    $this->prefix = NULL;
			}
		}   // end function setPrefix()

        /**
         *
         **/
		public function setRecursion( $bool ) {
		    if ( is_bool($bool) ) {
		        $this->recurse = $bool;
			}
		}   // end function setRecursion()

		/**
		 *
		 **/
		public function setSkipDirs( $dirs ) {
		    // reset
		    if ( is_null( $dirs ) ) {
		        $this->skip_dirs = array();
		        return;
			}
		    // make sure $dirs is an array
            if ( $dirs && is_scalar($dirs) ) {
                $dirs = array( $dirs );
			}
			if ( is_array($dirs) ) {
			    $this->skip_dirs = $dirs;
			}
		}   // end function setSkipDirs()

		/**
		 *
		 **/
		public function setSuffixFilter( $suffixes ) {
		    // reset
		    if ( is_null( $suffixes ) ) {
		        $this->suffix_filter = array();
		        return;
			}
		    // make sure $suffixes is an array
            if ( $suffixes && is_scalar($suffixes) ) {
                $suffixes = array( $suffixes );
			}
			if ( is_array($suffixes) ) {
			    $this->suffix_filter = $suffixes;
			}
		}   // end function setSuffixFilter()

		/**
		 * set directory or file to read-only; used for index.php
		 *
		 * @access public
		 * @param  string $directory
		 * @return void
		 *
		 **/
        public function setReadOnly($item) {
	        // Only chmod if os is not windows
	        if (OPERATING_SYSTEM != 'windows') {
                $mode = (int) octdec( '644' );
	            if (file_exists($item)) {
	                $umask = umask(0);
	                chmod($item, $mode);
	                umask($umask);
	                return true;
	            }
	            else {
	                return false;
	            }
	        }
	        else {
	            return true;
	        }
	    }   // function setReadOnly()

        /**
         * This method creates index.php files in every subdirectory of a given path
         *
         * @access public
         * @param  string  directory to start with
         * @return void
         *
         **/
        public function recursiveCreateIndex( $dir ) {
            if ( $handle = opendir($dir) ) {
                if ( ! file_exists( $dir . '/index.php' ) ) {
                    $fh = fopen( $dir.'/index.php', 'w' );
                    fwrite( $fh, '<' . '?' . 'php' . "\n" );
        	        //fwrite( $fh, $this->_class_secure_code() );
        	        fclose( $fh );
                }

                while ( false !== ( $file = readdir($handle) ) ) {
                    if ( $file != "." && $file != ".." ) {
                        if( is_dir( $dir.'/'.$file ) ) {
                            $this->recursiveCreateIndex( $dir.'/'.$file );
                        }
                    }
                }
                closedir($handle);
                return true;
            }
            else {
                return false;
            }

        }   // end function recursiveCreateIndex()

		/**
		 * remove directory recursively
		 *
		 * @access public
		 * @param  string  $directory
		 * @return boolean
		 *
		 **/
	    public function removeDirectory($directory) {
	        // If suplied dirname is a file then unlink it
	        if (is_file($directory)) {
	            return unlink($directory);
	        }
	        // Empty the folder
	        if (is_dir($directory)) {
	            $dir = dir($directory);
	            while (false !== $entry = $dir->read()) {
	                // Skip pointers
	                if ($entry == '.' || $entry == '..') {
	                    continue;
	                }
	                // recursive delete
	                if (is_dir($directory . '/' . $entry)) {
	                    $this->removeDirectory($directory . '/' . $entry);
	                }
	                else {
	                    unlink($directory . '/' . $entry);
	                }
	            }
	            // Now delete the folder
	            $dir->close();
	            return rmdir($directory);
	        }
	    }   // end function removeDirectory()

	    /**
	     * check if directory is world-writable
	     * hopefully more secure than is_writable()
	     *
	     * @access public
	     * @param  string  $directory
	     * @return boolean
	     *
	     **/
		public function is_world_writable($directory) {
		    if ( ! is_dir( $directory ) ) {
		        return false;
			}
		    return ( substr(sprintf('%o', fileperms($directory)), -1) == 7 ? true : false );
		}   // end function is_world_writable()


	    /**
	     *
	     **/
	    public function copyRecursive( $dirsource, $dirdest, $move = false ) {
		    if ( is_dir($dirsource) ) {
		        $dir_handle = opendir($dirsource);
		    }
		    else {
		        return false;
			}
			$errors = array();
		    if ( is_resource($dir_handle) ) {
			    while ( $file = readdir($dir_handle) ) {
			        if( $file != "." && $file != ".." ) {
			            if( ! is_dir($dirsource."/".$file) ) {
			                if ( ! copy ($dirsource."/".$file, $dirdest.'/'.$file) ) {
                                $errors[] = error_get_last();
							}
			            }
			            else {
			                if ( ! make_dir($dirdest."/".$file) ) {
			                    $errors[] = error_get_last();
							}
				            $ret = $this->copyRecursive($dirsource."/".$file, $dirdest.'/'.$file);
				            if ( is_array($ret) ) {
				                $errors = array_merge( $errors, $ret );
							}
			            }
			        }
			    }
			    closedir($dir_handle);
			    if ( count($errors) ) {
			        $this->removeRecursive( $dirdest );
			        return false;
				}
			    if ( $move ) {
			        $this->removeRecursive( $dirsource );
			    }
				return true;
			}
			else {
			    return false;
			}
		}   // end function _copyRecursive()

		/**
		 *
		 *
		 *
		 *
		 **/
		function removeRecursive( $directory ) {
			// If suplied dirname is a file then unlink it
			if ( is_file( $directory ) ) {
				return unlink($directory);
			}
			if ( is_dir( $directory ) ) {
				$dir = dir($directory);
				while ( false !== ( $entry = $dir->read() ) ) {
                	if ( $entry == '.' || $entry == '..' ) {
                    	continue;
                	}
                	if ( is_dir($directory . '/' . $entry) ) {
                    	$this->removeRecursive($directory . '/' . $entry);
					}
					else {
                    	unlink($directory . '/' . $entry);
					}
				}
				// Now delete the folder
				$dir->close();
				return @rmdir($directory);
			}
    	}   // end function removeRecursive()

}


?>
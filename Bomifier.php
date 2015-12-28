<?php

/**
 * Bomifier class.
 * @author _alK13
 */
class Bomifier
{

    /**
     * The file path.
     * @var string
     */
    protected $uri;

    /**
     * The uri is null to assume backward compatibility.
     * @param string $uri
     */
    public function __construct($uri = null)
    {
        $this->setUri($uri);
    }

    /**
     * Return the BOM if the file if it exists.
     * @return int|string
     */
    public function detect()
    {
        $str = file_get_contents($this->uri);

        foreach ($this->getBom('all') as $encoding => $bom) {
            if (0 === strncmp($str, $bom, strlen($bom))) {
                return $encoding;
            }
        }
    }

    /**
     * Add a BOM at the beginning of the file.
     * @param string $encoding
     * @return int
     */
    public function add($encoding = 'UTF-8')
    {
        $str = file_get_contents($this->uri);
        return file_put_contents($this->uri, $this->getBom($encoding) . $str);
    }

    /**
     * Remove the given BOM from the file.
     * @param string $encoding
     * @return int
     */
    public function remove($encoding)
    {
        $str = file_get_contents($this->uri);
        return file_put_contents($this->uri, substr($str, (strlen($this->getBom($encoding)))));
    }

    /**
     * Return a BOM string or an array of all BOM.
     * @param string $encoding
     * @return array|string
     */
    public function getBom($encoding = 'UTF-8')
    {
        $boms = array(
            'UTF-8'                => pack('CCC', 0xef, 0xbb, 0xbf),
            'UTF-16 Big Endian'    => pack('CC', 0xfe, 0xff),
            'UTF-16 Little Endian' => pack('CC', 0xff, 0xfe),
            'UTF-32 Big Endian'    => pack('CCCC', 0x00, 0x00, 0xfe, 0xff),
            'UTF-32 Little Endian' => pack('CCCC', 0xff, 0xfe, 0x00, 0x00),
            'SCSU'                 => pack('CCC', 0x0e, 0xfe, 0xff),
            'UTF-7 (1)'            => pack('CCCC', 0x2b, 0x2f, 0x76, 0x38),
            'UTF-7 (2)'            => pack('CCCC', 0x2b, 0x2f, 0x76, 0x39),
            'UTF-7 (3)'            => pack('CCCC', 0x2b, 0x2f, 0x76, 0x2b),
            'UTF-7 (4)'            => pack('CCCC', 0x2b, 0x2f, 0x76, 0x2f),
            'UTF-7 (5)'            => pack('CCCCC', 0x2b, 0x2f, 0x76, 0x38, 0x2d),
            'UTF-1'                => pack('CCC', 0xF7, 0x64, 0x4c),
            'UTF-EBCDIC'           => pack('CCCC', 0xdd, 0x73, 0x66, 0x73),
            'BOCU-1'               => pack('CCC', 0xfb, 0xee, 0x28),
            'GB-18030'             => pack('CCCC', 0x84, 0x31, 0x95, 0x33),
        );

        if ('all' == $encoding) {
            return $boms;
        }
        return $boms[$encoding];
    }

    /**
     * Get the URI file.
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Set the URI File
     * @param $uri
     * @return $this
     * @throws Exception
     */
    public function setUri($uri)
    {
        if (!empty($uri) && !is_file($uri)) {
            throw new \Exception(sprintf('File %s not found.', $uri));
        }
        $this->uri = $uri;
        return $this;
    }

}

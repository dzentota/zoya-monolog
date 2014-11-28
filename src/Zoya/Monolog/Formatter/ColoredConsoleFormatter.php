<?php
namespace Zoya\Monolog\Formatter;

use Monolog\Formatter\LineFormatter;
use Monolog\Logger;

/**
 * Based on Symfony\Component\Console\Formatter\OutputFormatterStyle
 * Class ColoredConsoleFormatter
 * @package Zoya\Monolog\Formatter
 */
class ColoredConsoleFormatter extends LineFormatter
{
    /**
     * @var array
     */
    private static $availableForegroundColors = array(
        'black' => array('set' => 30, 'unset' => 39),
        'red' => array('set' => 31, 'unset' => 39),
        'green' => array('set' => 32, 'unset' => 39),
        'yellow' => array('set' => 33, 'unset' => 39),
        'blue' => array('set' => 34, 'unset' => 39),
        'magenta' => array('set' => 35, 'unset' => 39),
        'cyan' => array('set' => 36, 'unset' => 39),
        'white' => array('set' => 37, 'unset' => 39)
    );
    /**
     * @var array
     */
    private static $availableBackgroundColors = array(
        'black' => array('set' => 40, 'unset' => 49),
        'red' => array('set' => 41, 'unset' => 49),
        'green' => array('set' => 42, 'unset' => 49),
        'yellow' => array('set' => 43, 'unset' => 49),
        'blue' => array('set' => 44, 'unset' => 49),
        'magenta' => array('set' => 45, 'unset' => 49),
        'cyan' => array('set' => 46, 'unset' => 49),
        'white' => array('set' => 47, 'unset' => 49)
    );
    /**
     * @var array
     */
    private static $availableOptions = array(
        'bold' => array('set' => 1, 'unset' => 22),
        'underscore' => array('set' => 4, 'unset' => 24),
        'blink' => array('set' => 5, 'unset' => 25),
        'reverse' => array('set' => 7, 'unset' => 27),
        'conceal' => array('set' => 8, 'unset' => 28)
    );

    /**
     * Default colors map
     * @var array
     */
    private $colorsMap = [
        Logger::DEBUG => [self::COLOR_FOREGROUND => 'white', self::COLOR_BACKGROUND => 'cyan', self::OPTIONS => []],
        Logger::INFO => [self::COLOR_FOREGROUND => 'green', self::COLOR_BACKGROUND => null, self::OPTIONS => []],
        Logger::NOTICE => [self::COLOR_FOREGROUND => 'yellow', self::COLOR_BACKGROUND => null, self::OPTIONS => []],
        Logger::WARNING => [
            self::COLOR_FOREGROUND => 'yellow',
            self::COLOR_BACKGROUND => null,
            self::OPTIONS => ['bold']
        ],
        Logger::ERROR => [self::COLOR_FOREGROUND => 'white', self::COLOR_BACKGROUND => 'red', self::OPTIONS => []],
        Logger::CRITICAL => [
            self::COLOR_FOREGROUND => 'white',
            self::COLOR_BACKGROUND => 'red',
            self::OPTIONS => ['bold']
        ],
        Logger::ALERT => [
            self::COLOR_FOREGROUND => 'white',
            self::COLOR_BACKGROUND => 'red',
            self::OPTIONS => ['bold']
        ],
        Logger::EMERGENCY => [
            self::COLOR_FOREGROUND => 'white',
            self::COLOR_BACKGROUND => 'red',
            self::OPTIONS => ['underscore']
        ]

    ];

    /**
     * Foreground color
     */
    const COLOR_FOREGROUND = 'foreground';
    /**
     * Background color
     */
    const COLOR_BACKGROUND = 'background';
    /**
     * Options, like bold, underline...
     */
    const OPTIONS = 'options';

    /**
     * @param null|string $colorsMap e.g [ Logger::ERROR => [ 'foreground' => 'white', 'background' => 'red', 'options' =>[] ] ]
     * @param null $format
     * @param null $dateFormat
     * @param bool $allowInlineLineBreaks
     */
    public function __construct(
        $colorsMap = null,
        $format = null,
        $dateFormat = null,
        $allowInlineLineBreaks = false
    ) {
        if (null !== $colorsMap) {
            $this->colorsMap = array_merge($this->colorsMap, $colorsMap);
        }
        if (is_null($format)) {
            $format = "%channel%: %message% [%datetime%] \n";
        }
        parent::__construct($format, $dateFormat, $allowInlineLineBreaks);
    }

    /**
     * Checks whether provided foreground color is valid
     * @param null $color
     * @throws \InvalidArgumentException
     */
    public function checkForeground($color = null)
    {
        if (null === $color) {
            return;
        }
        if (!isset(static::$availableForegroundColors[$color])) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid foreground color specified: "%s". Expected one of (%s)',
                $color,
                implode(', ', array_keys(static::$availableForegroundColors))
            ));
        }
    }

    /**
     * Checks whether provided background color is valid
     * @param null $color
     * @throws \InvalidArgumentException
     */
    public function checkBackground($color = null)
    {
        if (null === $color) {
            return;
        }
        if (!isset(static::$availableBackgroundColors[$color])) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid background color specified: "%s". Expected one of (%s)',
                $color,
                implode(', ', array_keys(static::$availableBackgroundColors))
            ));
        }
    }

    /**
     * Checks whether provided option is valid
     * @param $option
     * @throws \InvalidArgumentException
     */
    public function checkOption($option)
    {
        if (!isset(static::$availableOptions[$option])) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid option specified: "%s". Expected one of (%s)',
                $option,
                implode(', ', array_keys(static::$availableOptions))
            ));
        }

    }

    /**
     * Checks whether provided options are valid
     *
     * @param array $options
     */
    public function checkOptions(array $options)
    {
        foreach ($options as $option) {
            $this->checkOption($option);
        }
    }

    /**
     * @param array $record
     * @return array|mixed|string
     * @throws \RuntimeException
     */
    public function format(array $record)
    {

        $output = parent::format($record);
        if (!isset($this->colorsMap[$record['level']])) {
            throw new \RuntimeException('No color map found for log level ' . $record['level']);
        }
        $colors = $this->colorsMap[$record['level']];

        $setCodes = array();
        $unsetCodes = array();

        if (null !== $colors[self::COLOR_FOREGROUND]) {
            $foregroundColor = $colors[self::COLOR_FOREGROUND];
            $this->checkForeground($foregroundColor);
            $setCodes[] = self::$availableForegroundColors[$foregroundColor]['set'];
            $unsetCodes[] = self::$availableForegroundColors[$foregroundColor]['unset'];
        }

        if (null !== $colors[self::COLOR_BACKGROUND]) {
            $backgroundColor = $colors[self::COLOR_BACKGROUND];
            $this->checkBackground($backgroundColor);
            $setCodes[] = self::$availableForegroundColors[$backgroundColor]['set'];
            $unsetCodes[] = self::$availableForegroundColors[$backgroundColor]['unset'];
        }

        $options = $colors[self::OPTIONS];
        if (count($options)) {
            $this->checkOptions($options);
            foreach ($options as $option) {
                $opt = self::$availableOptions[$option];
                $setCodes[] = $opt['set'];
                $unsetCodes[] = $opt['unset'];
            }
        }
        if (0 === count($setCodes)) {
            return $output;
        }
        return sprintf(
            "\033[%sm%s\033[%sm ",
            implode(';', $setCodes),
            "[{$record['level_name']}]",
            implode(';', $unsetCodes)
        ) . $output;
    }

}


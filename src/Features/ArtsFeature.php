<?php

namespace Laravilt\Plugins\Features;

/**
 * Generates arts folder with cover photo for the package.
 *
 * Creates an arts directory with a placeholder screenshot.jpg.
 */
class ArtsFeature extends AbstractFeature
{
    public function getName(): string
    {
        return 'arts';
    }

    public function shouldGenerate(array $config): bool
    {
        return $config['generate_arts'] ?? false;
    }

    public function getPriority(): int
    {
        return 55; // Asset files - after CSS/JS
    }

    public function getDirectories(array $config): array
    {
        return $this->shouldGenerate($config)
            ? ['arts']
            : [];
    }

    public function generate(array $config): void
    {
        $artsPath = $config['base_path'].'/arts';

        // Ensure arts directory exists
        if (! is_dir($artsPath)) {
            mkdir($artsPath, 0755, true);
        }

        // Generate placeholder screenshot
        $this->generatePlaceholderScreenshot($artsPath, $config);
    }

    protected function generatePlaceholderScreenshot(string $artsPath, array $config): void
    {
        $screenshotPath = $artsPath.'/screenshot.jpg';

        // Create a 1200x630 image (standard social media preview size)
        $width = 1200;
        $height = 630;
        $image = imagecreatetruecolor($width, $height);

        // Create dark background with subtle pattern
        $this->createDarkBackground($image, $width, $height);

        // Add plugin icon (rounded square with cyan background)
        $this->addPluginIcon($image, $width, $height);

        // Add plugin name text
        $pluginName = $config['studly_name'];
        $this->addTitle($image, $pluginName, $width, $height);

        // Add description/subtitle
        $description = $config['description'] ?? 'Laravel package for Laravilt';
        $this->addDescription($image, $description, $width, $height);

        // Add Laravilt branding at bottom
        $this->addBranding($image, $width, $height);

        // Save as JPEG
        imagejpeg($image, $screenshotPath, 95);
        imagedestroy($image);
    }

    protected function createDarkBackground($image, int $width, int $height): void
    {
        // Dark background (similar to #0f0f0f or #1a1a1a)
        $darkBg = imagecolorallocate($image, 26, 26, 26);
        imagefilledrectangle($image, 0, 0, $width, $height, $darkBg);

        // Add subtle pattern with circles
        $patternColor = imagecolorallocate($image, 35, 35, 35);
        for ($i = 0; $i < 15; $i++) {
            $circleSize = rand(100, 300);
            $x = rand(-$circleSize, $width);
            $y = rand(-$circleSize, $height);
            imageellipse($image, $x, $y, $circleSize, $circleSize, $patternColor);
        }

        // Add bottom gradient bar (cyan accent)
        $cyanColor = imagecolorallocate($image, 103, 232, 249);
        imagefilledrectangle($image, 0, $height - 8, $width, $height, $cyanColor);
    }

    protected function addPluginIcon($image, int $width, int $height): void
    {
        // Draw rounded square icon with cyan background
        $iconSize = 120;
        $iconX = ($width - $iconSize) / 2;
        $iconY = 120;

        $cyan = imagecolorallocate($image, 103, 232, 249);
        $darkIcon = imagecolorallocate($image, 30, 30, 30);

        // Draw rounded rectangle (simplified as rectangle with GD)
        imagefilledrectangle($image, $iconX, $iconY, $iconX + $iconSize, $iconY + $iconSize, $cyan);

        // Draw plug icon (simplified)
        $plugSize = 60;
        $plugX = ($width - $plugSize) / 2;
        $plugY = $iconY + 30;

        // Plug body
        imagefilledrectangle($image, $plugX + 15, $plugY + 20, $plugX + $plugSize - 15, $plugY + 50, $darkIcon);

        // Plug prongs
        imagefilledrectangle($image, $plugX + 20, $plugY, $plugX + 28, $plugY + 20, $darkIcon);
        imagefilledrectangle($image, $plugX + 32, $plugY, $plugX + 40, $plugY + 20, $darkIcon);
    }

    protected function addTitle($image, string $text, int $width, int $height): void
    {
        // Use built-in font (size 5 is the largest built-in)
        $fontSize = 5;
        $charWidth = imagefontwidth($fontSize);
        $charHeight = imagefontheight($fontSize);

        // Calculate text dimensions
        $textWidth = $charWidth * strlen($text);

        // Center the text below the icon
        $x = ($width - $textWidth) / 2;
        $y = 270;

        // Cyan color for title
        $cyan = imagecolorallocate($image, 103, 232, 249);

        // Draw each character with spacing for better look
        for ($i = 0; $i < strlen($text); $i++) {
            imagechar($image, $fontSize, $x + ($i * $charWidth), $y, $text[$i], $cyan);
        }
    }

    protected function addDescription($image, string $text, int $width, int $height): void
    {
        // Split long text into lines
        $maxLineLength = 80;
        $lines = [];
        $words = explode(' ', $text);
        $currentLine = '';

        foreach ($words as $word) {
            if (strlen($currentLine.' '.$word) <= $maxLineLength) {
                $currentLine .= ($currentLine ? ' ' : '').$word;
            } else {
                if ($currentLine) {
                    $lines[] = $currentLine;
                }
                $currentLine = $word;
            }
        }
        if ($currentLine) {
            $lines[] = $currentLine;
        }

        // Limit to 2 lines
        $lines = array_slice($lines, 0, 2);

        $fontSize = 3;
        $lightCyan = imagecolorallocate($image, 147, 197, 253);
        $startY = 320;

        foreach ($lines as $index => $line) {
            $textWidth = imagefontwidth($fontSize) * strlen($line);
            $x = ($width - $textWidth) / 2;
            $y = $startY + ($index * 20);

            imagestring($image, $fontSize, $x, $y, $line, $lightCyan);
        }
    }

    protected function addBranding($image, int $width, int $height): void
    {
        $brandText = 'Laravilt';
        $fontSize = 4;
        $textWidth = imagefontwidth($fontSize) * strlen($brandText);

        $x = ($width - $textWidth) / 2;
        $y = $height - 80;

        // Gradient colors (purple to cyan)
        $purple = imagecolorallocate($image, 168, 85, 247);
        $cyan = imagecolorallocate($image, 103, 232, 249);

        // Draw text
        imagestring($image, $fontSize, $x, $y, $brandText, $cyan);
    }
}

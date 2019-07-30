<?php

namespace iFlair\LetsBonusFrontBundle\Resizer;

use Gaufrette\File;
use Imagine\Image\Box;
use Imagine\Image\ImagineInterface;
use Sonata\MediaBundle\Metadata\MetadataBuilderInterface;
use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Resizer\ResizerInterface;

class CustomResizer implements ResizerInterface
{
    /**
     * ImagineInterface.
     */
    protected $adapter;

    /**
     * string.
     */
    protected $mode;
    protected $metadata;

    /**
     * @param ImagineInterface $adapter
     * @param string $mode
     * @param MetadataBuilderInterface $metadata
     */
    public function __construct(ImagineInterface $adapter, $mode, MetadataBuilderInterface $metadata)
    {
        $this->adapter = $adapter;
        $this->mode = $mode;
        $this->metadata = $metadata;
    }

    /**
     * {@inheritdoc}
     * @throws \RuntimeException
     * @throws \Imagine\Exception\InvalidArgumentException
     * @throws \Imagine\Exception\RuntimeException
     * @throws \Gaufrette\Exception\FileNotFound
     */
    public function resize(MediaInterface $media, File $in, File $out, $format, array $settings)
    {
        if (!isset($settings['width'])) {
            throw new \RuntimeException(
                sprintf(
                    'Width parameter is missing in context "%s" for provider "%s"',
                    $media->getContext(),
                    $media->getProviderName()
                )
            );
        }

        $image = $this->adapter->load($in->getContent());
        $size = $media->getBox();

        if (null !== $settings['height'] && $settings['height'] > 0) {
            $ratioWidth = $size->getWidth() / $settings['width'];
            $ratioHeight = $size->getHeight() / $settings['height'];
            $ratio = $ratioHeight > $ratioWidth ? $ratioWidth : $ratioHeight;
            $image->resize(new Box($settings['width'] * $ratio, $settings['height'] * $ratio));
            $size = $image->getSize();
        }

        $settings['height'] = (int) ($settings['width'] * $size->getHeight() / $size->getWidth());

        if ($settings['height'] < $size->getHeight() && $settings['width'] < $size->getWidth()) {
            $content = $image
                ->thumbnail(new Box($settings['width'], $settings['height']), $this->mode)
                ->get($format, ['quality' => $settings['quality']]);
        } else {
            $content = $image->get($format, ['quality' => $settings['quality']]);
        }

        $out->setContent($content, $this->metadata->get($media, $out->getName()));
    }

    /**
     * {@inheritdoc}
     * @throws \Imagine\Exception\InvalidArgumentException
     */
    public function getBox(MediaInterface $media, array $settings)
    {
        $size = $media->getBox();

        if (null !== $settings['height']) {
            if ($size->getHeight() > $size->getWidth()) {
                $higher = $size->getHeight();
                $lower = $size->getWidth();
            } else {
                $higher = $size->getWidth();
                $lower = $size->getHeight();
            }

            if ($higher - $lower > 0) {
                return new Box($lower, $lower);
            }
        }

        $settings['height'] = (int) ($settings['width'] * $size->getHeight() / $size->getWidth());

        if ($settings['height'] < $size->getHeight() && $settings['width'] < $size->getWidth()) {
            return new Box($settings['width'], $settings['height']);
        }

        return $size;
    }
}

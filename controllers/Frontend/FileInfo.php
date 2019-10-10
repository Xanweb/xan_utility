<?php
namespace XanUtility\Controller\Frontend;

use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Concrete\Core\Entity\File\File as FileEntity;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Page\Page as ConcretePage;
use Concrete\Core\File\File as ConcreteFile;
use Permissions;
use Controller;

class FileInfo extends Controller
{
    /**
     * @var FileEntity
     */
    protected $file;

    protected $permissions;

    public function shouldRunControllerTask()
    {
        return $this->permissions->canViewFileInFileManager();
    }

    public function on_start()
    {
        // Set Current Page if cID is set. Useful for detecting active locale
        $cID = $this->request->get('cID');
        if ($this->app['helper/validation/numbers']->integer($cID)) {
            $page = ConcretePage::getByID($cID);
            if (!is_object($page) || $page->getError() == COLLECTION_NOT_FOUND) {
                throw new UserMessageException(t('Unable to find the specified page.'));
            }

            if ($page->isError()) {
                throw new \Exception(t('Access Denied'));
            }

            $this->request->setCurrentPage($page);
        }

        $file = null;
        $fID = $this->app['helper/security']->sanitizeInt($this->request->get('fID'));
        if ($fID) {
            $file = ConcreteFile::getByID($fID);
        }

        if (is_object($file)) {
            $this->setFileObject($file);
        } else {
            throw new UserMessageException(t('Invalid file.'));
        }
    }

    private function setFileObject(FileEntity $f)
    {
        $this->file = $f;
        $this->permissions = new Permissions($this->file);
    }

    public function getJSON()
    {
        $properties = $this->request->get('properties');
        $attributes = $this->request->get('attributes');
        if (!is_array($attributes) && !is_array($properties)) {
            return false;
        }

        $result = ['properties' => [], 'attributes' => []];
        if (is_object($this->file)) {
            $fv = $this->file->getApprovedVersion();
            $allowedProperties = $this->getAllowedFileProperties();
            foreach ((array) $properties as $field) {
                if (isset($allowedProperties[$field])) {
                    if ($allowedProperties[$field] instanceof \Closure) {
                        $attrValue = call_user_func($allowedProperties[$field], $fv);
                    } else {
                        $attrValue = $fv->{$allowedProperties[$field]}();
                    }

                    $result['properties'][$field] = $attrValue ?: '';
                } else {
                    throw new UserMessageException(t('Unsupported File Property: "%s".', $field));
                }
            }

            $fakc = $fv->getObjectAttributeCategory();
            foreach ((array) $attributes as $field) {
                $ak = $fakc->getAttributeKeyByHandle((string) $field);
                if (is_object($ak)) {
                    $attrValue = (string) $fv->getAttribute($ak, 'display');
                    $result['attributes'][$field] = $attrValue ?: '';
                } else {
                    \Log::warn(t('XanUtility::FileInfoController: Undefined File Attribute Key "%s".', $field));
                }
            }
        }

        return JsonResponse::create($result);
    }

    private function getAllowedFileProperties()
    {
        $urlResolver = $this->app->make(ResolverManagerInterface::class);

        return [
            'title' => 'getTitle',
            'description' => 'getDescription',
            'formatted_size' => 'getSize',
            'size' => 'getFullSize',
            'urlInline' => function ($fv) use ($urlResolver) {
                return (string) $urlResolver->resolve(['/download_file', 'view_inline', $fv->getFileID()]);
            },
            'urlDownload' => function ($fv) use ($urlResolver) {
                return (string) $urlResolver->resolve(['/download_file', 'view', $fv->getFileID()]);
            },
        ];
    }
}

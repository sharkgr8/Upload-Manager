<?php

Route::get('upload', function()
{
    return UploadManager::showForm();

});

Route::get('uploadServer', function()
{
    return UploadManager::processUpload();
});

Route::post('uploadServer', function()
{
    return UploadManager::processUpload();
});

Route::post('removeFiles', function()
{
    return UploadManager::removeFiles();
});
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

Route::post('saveToDB', function()
{
    return UploadManager::saveToDB();
});

Route::get('tags', function()
{
    return UploadManager::getTags();
});
/**
 * moderation.js created by samy-oak-tree (2020)
 *
 * This file is for admin.php. Right now it handles the category/group name changes and other basic CRUD operations on
 * the categories.
 */
$(document).ready(() => {
    $(".collapsible").on('click', evt => {
        let a = $(evt.target).parent();
        $(a.attr('href')).toggle();
        a.toggleClass("opened")
    })
    $(".category-name").each((_, categoryP) => {
        if (categoryP.addEventListener)
            categoryP.addEventListener('input', e => {
                let parent = $(categoryP).parent();
                if (!parent.find('.save-btn').length) {
                    let saveBtn = $('<button class="blue save-btn">Save</button>');
                    saveBtn.on('click', async e => {
                        saveBtn.prop("disabled", true);
                        await api({
                            object: 'category',
                            action: 'rename',
                            id: $(categoryP).attr('category-id'),
                            name: $(categoryP).text()
                        })
                        location.reload();
                    })
                    parent.append(saveBtn);
                }
            }, false)
    })
    $(".group").each((_, groupH2) => {
        if (groupH2.addEventListener)
            groupH2.addEventListener('input', e => {
                let parent = $(groupH2).parent();
                if (!parent.find('.save-btn').length) {
                    let saveBtn = $('<button class="blue save-btn">Save</button>');
                    saveBtn.on('click', async e => {
                        saveBtn.prop("disabled", true);
                        await api( {
                            object: 'category',
                            action: 'change_group_name',
                            id: $(groupH2).attr('group-id'),
                            name: $(groupH2).text().trim()
                        })
                        location.reload();
                    })
                    parent.append(saveBtn);
                }
            }, false)
    })
    if(location.hash){
        $(location.hash).toggle()
    }
})

function api(opts) {
    return new Promise(resolve => {
        $.post("api.php", opts, resolve)
    })
}

async function moveCategory(id) {
    await api({
        object: 'category',
        action: 'move',
        id: id
    })
    location.reload();
}

async function toggleCategory(id) {
    await api({
        object: 'category',
        action: 'toggle',
        id: id
    });
    location.reload();
}
async function addEvent(){
    let name = prompt('Name of event:');
    await api({
        object: 'category',
        action: 'create',
        name
    });
    location.reload();
}
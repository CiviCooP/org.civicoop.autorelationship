{* HEADER *}

<div class="crm-block crm-form-block">

<table class="form-layout">
    <tr>
        <td class="label">{ts}Contact{/ts}</td>
        <td>{$contact.display_name}</td>
    </tr>

    {foreach from=$elementNames item=elementName}
        <tr>
            <td class="label">{$form.$elementName.label}</td>
            <td class="content">{$form.$elementName.html}</td>
        </tr>
    {/foreach}

</table>

<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="bottom"}
</div>

</div>
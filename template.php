<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<script type="text/javascript" src="/local/js/imask.js"></script>
<script type="text/javascript">
    var specs = <?=json_encode($arResult['SPEC']);?>;
    var cities = <?=json_encode($arResult['CITY']);?>;
</script>
<div class="cpgp-svc">
    <div class="cpgp-svc__info">
        <p>Заказ обратного звонка</p>
    </div>
    <form id="fCallback" name="fCallback" method="post" action="<?=POST_FORM_ACTION_URI;?>">
		<?=bitrix_sessid_post()?>
        <div class="cpgp-svc__form">
            <div class="cpgp-svc__form-column-left">
                <div class="cpgp-svc__block">

                    <div class='cpgp-svc__field w-100'>
                        <div class='cpgp-svc__list-additional'>
                            <h3>Кому позвонить (ФИО)</h3>
                            <div class='createfolder-items'>
                                <input type='text' id='user' name='user' value="<?=$arResult['USER']['NAME']?>">
                            </div>
                            <div class='alert alert-danger d-none' id='user_error'>Это поле необходимо заполнить</div>
                        </div>
                    </div>

                    <div class='cpgp-svc__field w-100'>
                        <div class='cpgp-svc__list-additional'>
                            <h3>Телефон</h3>
                            <div class="createfolder-items">
                                <input type="text" id="phoneMasked" value="<?=$arResult['USER']['PHONE']?>">
                                <small>Или введите другой номер в формате (код)ххххх или +7(ххх)ххххххх</small>
                                <input type="hidden" id="phone" name="phone" value="<?=$arResult['USER']['PHONE']?>">
                            </div>
                            <div style="display:none" class='alert alert-danger' id='phone_error'>Это поле необходимо заполнить</div>
                        </div>
                    </div>

                    <div class='cpgp-svc__field' id="time">
                        <h3>Цель обращения</h3>
                        <div class='cpgp-svc__field'>
                            <div class='cpgp-svc__list-additional selectize'>
                                <select name="specialist" id="specialist">
                                </select>
                            </div>
                            <div style="display:none" class='alert alert-danger' id='specialist_error'>Это поле необходимо заполнить</div>
                        </div>
                    </div>

                    <div class='cpgp-svc__field w-100'>
                        <div class='cpgp-svc__list-additional'>
                            <h3>Город</h3>
                            <div class='cpgp-svc__list-additional selectize'>
                                <select name="city" id="city">
                                    <option value="" disabled>Выберите город</option>
                                    <option value="Москва">Москва</option>
                                    <option value="Санкт-Петербург">Санкт-Петербург</option>
                                </select>
                            </div>
                            <div style="display:none" class='alert alert-danger' id='specialist_error'>Это поле необходимо заполнить</div>
                        </div>
                    </div>

                    <div class='cpgp-svc__field'>
                        <h3>Выберите медицинский центр</h3>
                        <div class='cpgp-svc__field'>
                            <div class='cpgp-svc__list-additional selectize'>
                                <select name="center" id="center">
                                </select>
                            </div>
                            <div style="display:none" class='alert alert-danger' id='center_error'>Это поле необходимо заполнить</div>
                        </div>
                    </div>

                    <div class="cpgp-svc__field w-100">
                        <div class="cpgp-svc__list-additional selectize">
                            <div class="cpgp-svc__list-additional">
                                <label class="g-checkbox">
                                    <input type="checkbox" id="agree" class="cpgp-svc__software-other-checkbox"><span>согласен(а) на обработку моих персональных данных</span>
                                </label>
                            </div>
                            <small>Для отправки заявки необходимо дать согласие на обработку персональных данных</small>
							<br/>
							<br/>
							<br/>
							<div style='color: #0079c2'><b>По вопросам организации медицинского обслуживания просим обращаться по тел.: (700) 4-38-28 или (812) 609-38-28,<br> а также по электронной почте: <a href="mailto:otdel.ms@medgaz.gazprom.ru">otdel.ms@medgaz.gazprom.ru</a></b></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class='cpgp-svc__form-column-right'>
                <div class='cpgp-svc__block cpgp-svc__userdata'>
                    <h3>Список заявок на обратный звонок</h3>(последние 5)
                    <div class='cpgp-svc__field'>
                        <div class='cpgp-svc__list-additional selectize'>
                            <?foreach ($arResult['CALL'] as $key => $item) :?>
                            <p><span><?=$arResult['CALL'][$key]['DATA']?></span><?=$arResult['CALL'][$key]['NOW']?>
                            <br>
                                Кому: <?=$arResult['CALL'][$key]['NAME']?><br>
                                Телефон: <?=$arResult['CALL'][$key]['PHONE']?><br>
	                            <?=$arResult['CALL'][$key]['SPECIALIST']?>, <?=$arResult['CALL'][$key]['MEDICAL_CENTER']?>
                            </p>
                            <?endforeach?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="cpgp-svc__actions">
            <input type="submit" form="fCallback" name="submit" id="submit" value="Отправить" disabled>
        </div>
    </form>
</div>

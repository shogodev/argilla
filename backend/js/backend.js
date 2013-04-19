function Backend()
{
  // Копируем аргументы.
  var args = Array.prototype.slice.call(arguments),
  // Callback всегда последний.
      callback = args.pop(),
  // Модули можно передавать в виде массива или как индивидуальные параметры.
      modules = (args[0] && typeof args[0] == "string") ? args: args[0],
      i;

  // Функция должна быть вызвана как конструктор.
  if (!(this instanceof Backend))
  {
    return new Backend(args, callback);
  }

  // Добавим указанные модули к this.
  // Если модуль не указан или указана "*" добавим все модули.
  if (!modules || modules === "*")
  {
    modules = [];
    for (i in Backend.modules)
    {
      if (Backend.modules.hasOwnProperty(i))
      {
        modules.push(i);
      }
    }
  }

  // Инициализируем модули.
  for (i = 0; i < modules.length; i++)
  {
    Backend.modules[modules[i]](this);
  }

  callback(this);
}

Backend.modules = {};

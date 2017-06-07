# Maestro

O Maestro é um framework PHP (>= 7.0) com suporte a MVC (Model-View-Controller) e DDD (Domain Driven Design).

## Instalação

1 - Clonar repositório git.

2 - Rodar ```composer install``` no diretório base.

3 - Fornecer direito de escrita para o servidor no diretório ```core/var```.

4 - Copiar ```core/conf/conf.php.dist``` para ```core/conf/conf.php```.
 
5 - Acessar app Guia (ex. ```http://localhost/maestro/index.php/guia/main```).


### Para usar o JTrace:

1 - Alterar ```core/conf/conf.php```:

```
'log',
    'level' => 2, // 0 (nenhum), 1 (apenas erros) ou 2 (erros e SQL)
    'handler' => "socket",
	    'peer' => '[Development machine IP]',
    'strict' => '',
    'port' => '[Jtrace port]'
),
```

2 - Executar JTrace em ```core/support/jtrace/JTrace.jar```.


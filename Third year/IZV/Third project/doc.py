#!/usr/bin/python3.10
# coding=utf-8
import pandas as pd
import matplotlib.pyplot as plt
import seaborn as sns

def get_data() -> pd.DataFrame:
    """ Nacteni dat """

    return pd.read_pickle("accidents.pkl.gz")

def make_graph(df: pd.DataFrame):
    """ Graf zobrazující nehody zaviněné řidičem kde se
    vyskytly drogy/alkohol s těžkým ublížením nebo usmrcením """
    # ziskam data kde nehodu zpusobil ridic pod vlivem a kde doslo k tezkemu zraneni nebo usmrceni
    # vybiram vzdy jen relevantni sloupce
    df = df.loc[((df["p11"] != 2) & (df["p11"] != 0)) & ((df["p12"] >= 201) & (df["p12"] <= 516)) & ((df["p13a"] > 0) | (df["p13b"] > 0)), ["region"]]
    df = df.groupby(["region"]).agg({"region": "count"})
    df.columns = ["Počet"]
    df = df.reset_index().sort_values(["Počet"], ascending=False)

    # vytvoreni grafu
    sns.set_theme()
    sns.set_style("darkgrid")
    plt.figure(figsize=(10, 6))
    grid = sns.barplot(data=df, x="region", y="Počet", palette="magma")

    # nastaveni grafu
    grid.bar_label(grid.containers[0])
    grid.set(xlabel='Kraje', ylabel='Počet nehod')
    grid.set(ylim=(0, 180))

    plt.savefig("fig.png")    

def make_table(df: pd.DataFrame):
    """ Tabulka zobrazující alkohol vs drogy podle hlavnich pricin nehod """

    # ziskam data kde nehodu zpusobil ridic pod vlivem a kde doslo k tezkemu zraneni nebo usmrceni
    df = df.loc[((df["p11"] != 2) & (df["p11"] != 0)) & ((df["p12"] >= 201) & (df["p12"] <= 516)) & ((df["p13a"] > 0) | (df["p13b"] > 0)), ["p11", "p12", "region"]]
    table_df = df.copy()
    table_df["p11"] = table_df["p11"].astype(str)
    # zmenim hodnoty pro lepsi interpretaci
    table_df["p11"] = table_df['p11'].replace(['1', '3', '4', '5', '6', '7', '8', '9'],
                                            ['Alkohol', 'Alkohol',
                                            'Alkohol a drogy', 'Drogy',
                                            'Alkohol', 'Alkohol',
                                            'Alkohol', 'Alkohol'])

    table_df["p12"] = table_df["p12"].astype(str)
    table_df["p12"] = table_df['p12'].replace(to_replace=r'2\w\w', regex=True, value='Nepřiměřená rychlost')
    table_df["p12"] = table_df['p12'].replace(to_replace=r'3\w\w', regex=True, value='Nesprávné předjíždění')
    table_df["p12"] = table_df['p12'].replace(to_replace=r'4\w\w', regex=True, value='Nedání přednosti')
    table_df["p12"] = table_df['p12'].replace(to_replace=r'5\w\w', regex=True, value='Nesprávný způsob jízdy')

    # vytvorim tabulku
    table = pd.pivot_table(data=table_df, index='p11', columns='p12', values='p12',
                            aggfunc="count", fill_value=0).rename_axis(index=None, columns=None)

    print(table)

def get_damages(df: pd.DataFrame):
    """ Zjisteni celkove skody na vozidlech
    v nehodach s alkoholem zavinenymi ridicem """

    # ziskam data nehod ktere byly zavineny ridicem pod vlivem
    df = df.loc[((df["p11"] != 2) & (df["p11"] != 0)) & ((df["p12"] >= 201) & (df["p12"] <= 516)) & ((df["p45a"] > 0)), ["p53"]]
    # sectu vsechny 
    # cislo je ve stokorunach, takze * 100 pro spravnou castku
    dmg = df["p53"].sum() * 100
    print("\nCelková škoda na vozidlech napáchaná řidiči pod vlivem: %d,- Kč" % dmg)

def get_car_with_highest_dmg(df: pd.DataFrame):
    """ Zjisteni ktera znacka mela nejvice skod a kolikrat se vyskytla """

    df = df.loc[((df["p11"] != 2) & (df["p11"] != 0)) & ((df["p12"] >= 201) & (df["p12"] <= 516)) & ((df["p45a"] > 0)), ["p45a", "p53"]]
    df2 = df.copy()
    # spocitam skody u kazdeho auta, seradim a vyberz to nejvyssi
    df = df.groupby(["p45a"]).agg({"p53": "sum"})
    df.columns = ["skoda"]
    df = df.reset_index().sort_values(["skoda"], ascending=False).set_index('p45a').rename_axis(index=None).head(1)

    print("\nZnačka auta s nejvyšší škodou v nehodách zaviněných řidiči pod vlivem: ")
    print(df)

    # spocitam vyskyt kazde znacky a vyberu tu nejvetsi
    df2 = df2.groupby(["p45a"]).agg({"p45a": "count"})
    df2.columns = ["pocet"]
    df2 = df2.reset_index().sort_values(["pocet"], ascending=False).set_index('p45a').rename_axis(index=None).head(1)

    print("\nZároveň se jedná o nejčastěji vyskytující se značku: ")
    print(df2)
    print("číslo 39 je značka Škoda")

def get_road_state(df: pd.DataFrame):
    """ Zjisteni nejcastejsiho stavu vozovky u nehod zavinenymi ridicem s alkoholem """

    df = df.loc[((df["p11"] != 2) & (df["p11"] != 0)) & ((df["p12"] >= 201) & (df["p12"] <= 516)), ["p16"]]
    df2 = df.copy()
    # zmena hodnot pro lepsi interpretaci
    df2["p16"] = df2["p16"].astype(str)
    df2["p16"] = df2['p16'].replace(['1', '2', '3', '4', '5', '6', '7', '8', '9', '0'],
                                    ['suchý povrch', 'suchý povrch', 'mokrý povrch', 'bláto',
                                    'náledí/sníh', 'náledí/sníh', 'kapalina', 'sněhová vrstva',
                                    'náhlá změna stavu', 'jiné'])

    # spocitam pocet ke kazde kategorii, seradim a vyberu nejvetsi
    df2 = df2.groupby("p16").agg({"p16": "count"})
    df2.columns = ["pocet"]
    df2 = (df2.reset_index().sort_values(["pocet"], ascending=False)
            .set_index('p16').rename_axis(index=None).head(1))

    print("\nNejčastější stav vozovky v nehodách zaviněných řidiči pod vlivem: ")
    print(df2)

def get_percentage(df: pd.DataFrame):
    """ Kolik procent závažných (těžké zranění/usmrcení) nehod zavinili ridici s alkoholem """

    # spocitam pocet radku u vybranych dat podle podminek
    # pod vlivem
    _alc = df.loc[((df["p11"] != 2) & (df["p11"] != 0)) & ((df["p12"] >= 201) &
                    (df["p12"] <= 516)) & ((df["p13a"] > 0) | (df["p13b"] > 0)), :].shape[0]

    # neni pod vlivem
    _else = df.loc[((df["p11"] != 0)) & ((df["p12"] >= 201) & (df["p12"] <= 516)) &
                    ((df["p13a"] > 0) | (df["p13b"] > 0)), :].shape[0]

    print("\npočet závažných nehod zaviněných řidičem pod vlivem: %d" % _alc)
    print("celkocý počet závažných nehod zaviněných řidičem: %d" %_else)
    percent = (_alc / _else) * 100
    print("což dělá %.1f%%" % percent)

# v odstavcich:
#       hodnota 1: celková škoda zavinena opilymi ridici
#       hodntoa 2: znacka auta s nejvyssi skodou
#       hodnota 3: nejcastejsi stav vozovky při nehodě
#       hodnota 4: % vsech fatálních nehod zavinenych opilym ridicem

if __name__ == "__main__":
    DF = get_data()
    make_graph(DF)
    make_table(DF)
    get_damages(DF)
    get_car_with_highest_dmg(DF)
    get_road_state(DF)
    get_percentage(DF)

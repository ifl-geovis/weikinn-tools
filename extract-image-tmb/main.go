package main

import (
	"bufio"
	"fmt"
	"log"
	"os"
	"sort"
	"strings"
)

func main() {

	// set working dir
	os.Chdir("../temp/weikinn")
	root, _ := os.Getwd()

	// Tambora file
	// read in tambora file
	tamboraFile, err := os.Open(root + "/weikinn_abfrage.csv")
	if err != nil {
		log.Fatal(err)
	}
	defer tamboraFile.Close()

	// create slice to store img names
	var imageNamesTambora []string

	// scan tamboraFile by line and write to slice
	scannerTmb := bufio.NewScanner(tamboraFile)
	for scannerTmb.Scan() {
		imgNamesRaw := strings.TrimPrefix(scannerTmb.Text(), "IMAGEFILES;;")
		imgNames := strings.Split(imgNamesRaw, ";;")
		for _, name := range imgNames {
			imageNamesTambora = append(imageNamesTambora, name)
		}
	}

	if err := scannerTmb.Err(); err != nil {
		log.Fatal(err)
	}

	sort.Strings(imageNamesTambora)

	// Hard disk file
	// read in file
	file, err := os.Open(root + "/dateinamen.txt")
	if err != nil {
		log.Fatal(err)
	}
	defer file.Close()

	scanner := bufio.NewScanner(file)
	for scanner.Scan() {
		target := strings.Split(scanner.Text(), "\\")[3]
		i := sort.Search(len(imageNamesTambora),
			func(i int) bool { return imageNamesTambora[i] >= target })
		if i < len(imageNamesTambora) && imageNamesTambora[i] == target {
			//fmt.Printf("found \"%s\" at imageNamesTambora[%d]\n", imageNamesTambora[i], i)
		} else {
			fmt.Printf("%s not found \n", target)
		}

	}

	if err := scanner.Err(); err != nil {
		log.Fatal(err)
	}

}
